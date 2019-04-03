<?php

namespace Laravel\Nova\Tests\Fixtures;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class CommentResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = Comment::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'body',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make('ID', 'id'),
            MorphTo::make('Commentable', 'commentable')->display([PostResource::class => function ($resource) {
                return $resource->title;
            }])->types([
                PostResource::class => 'Post',
            ])->searchable(),
            BelongsTo::make('Author', 'author', UserResource::class),
            Text::make('Body', 'body')->rules('required', 'string', 'max:255'),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [new IdFilter];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [new NoopAction];
    }

    /**
     * Build a "relatable" query for the posts.
     *
     * @param NovaRequest $request
     * @param  Builder  $query
     * @return Builder
     */
    public static function relatablePosts(NovaRequest $request, $query)
    {
        if (! isset($_SERVER['nova.comment.useCustomRelatablePosts'])) {
            return PostResource::relatableQuery($request, $query);
        }

        $_SERVER['nova.comment.relatablePosts'] = $query;

        return $query->where('id', '<', 3);
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'comments';
    }
}
