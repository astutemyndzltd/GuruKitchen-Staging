<?php

namespace App\Notifications;

use App\Models\Order;
use FontLib\Table\Type\name;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Benwilkins\FCM\FcmMessage;

class OrderAccepted extends Notification
{
    use Queueable;
    /**
     * @var Order
     */
    private $order;
    private $driverName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $order, $driverName)
    {
        //
        $this->order = $order;
        $this->driverName = $driverName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','fcm'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toFcm($notifiable)
    {
        $message = new FcmMessage();

        $notification = [
            'title' => $this->driverName . " has accepted the order #" . $this->order->id,
            'body'         => $this->order->user->name,
            'icon'         => $this->order->foodOrders[0]->food->restaurant->getFirstMediaUrl('image', 'thumb'),
            'click_action' => "FLUTTER_NOTIFICATION_CLICK",
            'id' => '1',
            'status' => 'done',
			'sound' => 'default'
        ];

        $message->content($notification)->data($notification)->priority(FcmMessage::PRIORITY_HIGH);

        return $message;
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
            'order_id' => $this->order['id'],
        ];
    }
}
