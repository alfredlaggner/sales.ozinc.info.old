<?php

    namespace App\Notifications;

    use App\Invoice;
    use App\Payment;
    use App\Customer;
    use Illuminate\Bus\Queueable;
    use Illuminate\Notifications\Notification;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Notifications\Messages\MailMessage;

    class customerStatements extends Notification implements ShouldQueue
    {
        use Queueable;

        /**
         * Create a new notification instance.
         *
         * @return void
         */
        public $customer;

        public function __construct(Customer $customer)
        {
            $this->customer = $customer;
        }

        /**
         * Get the notification's delivery channels.
         *
         * @param mixed $notifiable
         * @return array
         */
        public function via($notifiable)
        {
            return ['mail'];
        }

        /**
         * Get the mail representation of the notification.
         *
         * @param mixed $notifiable
         * @return \Illuminate\Notifications\Messages\MailMessage
         */
        public function toMail($notifiable)
        {
            //  dd( $this->customer->ext_id);
            $customer_id = $this->customer->ext_id;
            $customer_name = $this->customer->name;

            $ledger = [];
            $total_amount = 0;
            $query = Invoice::where('customer_id', $customer_id)
                ->get();
            //    dd($query);
            $total_residual = 0;
            foreach ($query as $q) {
                $total_amount += $q->amount_total;
                $total_residual += $q->residual;
                array_push($ledger, [
                        'sales_order' => $q->sales_order,
                        'date' => $q->invoice_date,
                        'name' => $q->name,
                        'due' => $q->due_date,
                        'payment_date' => 0,
                        'amount' => $q->amount_total,
                        'payment_amount' => 0,
                        'residual' => $q->residual,
                        'difference' => 0]
                );
            }
            $query = Payment::whereYear('payment_date', 2019)
                ->where('customer_id', $customer_id)
                ->get();
            $total_payment = 0;
            foreach ($query as $q) {
                $total_payment += $q->amount;
                array_push($ledger, [
                        'sales_order' => '',
                        'date' => $q->payment_date,
                        'name' => $q->move_name,
                        'due' => '',
                        'payment_date' => $q->payment_date,
                        'amount' => 0,
                        'payment_amount' => $q->amount,
                        'residual' => 0,
                        'difference' => $q->payment_difference]
                );

            }
            //     dd("xxx");
            $out_ledger = collect($ledger);
            $ledgers = $out_ledger->sortByDesc('date');
            //  dd($ledgers);
            //  dd($out_ledger);

            return (new MailMessage)->subject('Invoices overdue')->markdown('mail.emailCustomerStatements', compact('ledgers', 'total_amount', 'total_residual', 'total_payment', 'customer_name'));
        }

        /**
         * Get the array representation of the notification.
         *
         * @param mixed $notifiable
         * @return array
         */
        public function toArray($notifiable)
        {
            return [
                //
            ];
        }
    }
