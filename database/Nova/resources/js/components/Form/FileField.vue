<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <div v-if="hasValue" class="mb-6">
                <template v-if="shouldShowLoader">
                    <ImageLoader
                        :src="imageUrl"
                        :maxWidth="maxWidth"
                        @missing="value => (missing = value)"
                    />
                </template>

                <template v-if="field.value && !imageUrl">
                    <card
                        class="flex item-center relative border border-lg border-50 overflow-hidden p-4"
                    >
                        <span class="truncate mr-3"> {{ field.value }} </span>

                        <DeleteButton
                            :dusk="field.attribute + '-internal-delete-link'"
                            class="ml-auto"
                            v-if="shouldShowRemoveButton"
                            @click="confirmRemoval"
                        />
                    </card>
                </template>

                <p v-if="imageUrl" class="mt-3 flex items-center text-sm">
                    <DeleteButton
                        :dusk="field.attribute + '-delete-link'"
                        v-if="shouldShowRemoveButton"
                        @click="confirmRemoval"
                    >
                        <span class="class ml-2 mt-1"> {{ __('Delete') }} </span>
                    </DeleteButton>
                </p>

                <portal to="modals">
                    <transition name="fade">
                        <confirm-upload-removal-modal
                            v-if="removeModalOpen"
                            @confirm="removeFile"
                            @close="closeRemoveModal"
                        />
                    </transition>
                </portal>
            </div>

            <span class="form-file mr-4">
                <input
                    ref="fileField"
                    :dusk="field.attribute"
                    class="form-file-input"
                    type="file"
                    :id="idAttr"
                    name="name"
                    @change="fileChange"
                />
                <label :for="labelFor" class="form-file-btn btn btn-default btn-primary">
                    {{ __('Choose File') }}
                </label>
            </span>

            <span class="text-gray-50"> {{ currentLabel }} </span>

            <p v-if="hasError" class="text-xs mt-2 text-danger">{{ firstError }}</p>
        </template>
    </default-field>
</template>

<script>
import ImageLoader from '@/components/ImageLoader'
import DeleteButton from '@/components/DeleteButton'
import { FormField, HandlesValidationErrors, Errors } from 'laravel-nova'

export default {
    props: ['resourceId', 'relatedResourceName', 'relatedResourceId', 'viaRelationship'],

    mixins: [HandlesValidationErrors, FormField],

    components: { DeleteButton, ImageLoader },

    data: () => ({
        file: null,
        fileName: '',
        removeModalOpen: false,
        missing: false,
        deleted: false,
        uploadErrors: new Errors(),
    }),

    mounted() {
        this.field.fill = formData => {
            if (this.file) {
                formData.append(this.field.attribute, this.file, this.fileName)
            }
        }
    },

    methods: {
        /**
         * Responsd to the file change
         */
        fileChange(event) {
            let path = event.target.value;
            let fileName = path.match(/[^\\/]*$/)[0];
            this.fileName = fileName;
            this.file = this.$refs.fileField.files[0]
        },

        /**
         * Confirm removal of the linked file
         */
        confirmRemoval() {
            this.removeModalOpen = true
        },

        /**
         * Close the upload removal modal
         */
        closeRemoveModal() {
            this.removeModalOpen = false
        },

        /**
         * Remove the linked file from storage
         */
        async removeFile() {
            this.uploadErrors = new Errors();

            const {
                resourceName,
                resourceId,
                relatedResourceName,
                relatedResourceId,
                viaRelationship,
            } = this;
            const attribute = this.field.attribute;

            const uri = this.viaRelationship
                ? `/nova-api/${resourceName}/${resourceId}/${relatedResourceName}/${relatedResourceId}/field/${attribute}?viaRelationship=${viaRelationship}`
                : `/nova-api/${resourceName}/${resourceId}/field/${attribute}`;

            try {
                await Nova.request().delete(uri);
                this.closeRemoveModal();
                this.deleted = true;
                this.$emit('file-deleted')
            } catch (error) {
                this.closeRemoveModal();

                if (error.response.status == 422) {
                    this.uploadErrors = new Errors(error.response.data.errors)
                }
            }
        },
    },

    computed: {
        hasError() {
            return this.uploadErrors.has(this.fieldAttribute)
        },

        firstError() {
            if (this.hasError) {
                return this.uploadErrors.first(this.fieldAttribute)
            }
        },

        /**
         * The current label of the file field
         */
        currentLabel() {
            return this.fileName || this.__('no file selected')
        },

        /**
         * The ID attribute to use for the file field
         */
        idAttr() {
            return this.labelFor
        },

        /**
         * The label attribute to use for the file field
         * @return {[type]} [description]
         */
        labelFor() {
            return `file-${this.field.attribute}`
        },

        /**
         * Determine whether the field has a value
         */
        hasValue() {
            return (
                Boolean(this.field.value || this.imageUrl) &&
                !Boolean(this.deleted) &&
                !Boolean(this.missing)
            )
        },

        /**
         * Determine whether the field should show the loader component
         */
        shouldShowLoader() {
            return !Boolean(this.deleted) && Boolean(this.imageUrl)
        },

        /**
         * Determine whether the field should show the remove button
         */
        shouldShowRemoveButton() {
            return Boolean(this.field.deletable)
        },

        imageUrl() {
            return this.field.previewUrl || this.field.thumbnailUrl
        },

        maxWidth() {
            return this.field.maxWidth || 320
        },
    },
}
</script>
