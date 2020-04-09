<?php

namespace Philip0514\Ark\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\Debug\Exception\FatalThrowableError;

use Philip0514\Ark\Models\MailTemplate;
use Philip0514\Ark\Models\Mail;

class Mailer extends Mailable
{
    use Queueable, SerializesModels;

    public $data, $template, $mail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $type_id = $this->data['type_id'];
        $data = $this->data['data'];
        $user = $data['user'];

        $this->mail = new Mail();
        $this->template = new MailTemplate();

        $rows1 = $this->template
        ->where('type', $type_id)
        ->where('display', '=', 1)
        ->where(function ($query) {
            $query->where('start_time', '<=', time())
                  ->orWhere('start_time', '=', 0)
                  ->orWhereNULL('start_time');
        })
        ->where(function ($query) {
            $query->where('end_time', '>', time())
                  ->orWhere('end_time', '=', 0)
                  ->orWhereNULL('end_time');
        })
        ->orderBy('id', 'desc')
        ->first()->toArray();


        $title = $rows1['title'];
        $title = Blade::compileString($title);
        $title = $this->renderHTML($title, $data);
        $this->subject($title);

        $blade = htmlspecialchars_decode($rows1['content']);
        $content = Blade::compileString($blade);
        $content = $this->renderHTML($content, $data);

        if($rows1['from_email']){
            if($rows1['from_name']){
                $this->from($rows1['from_email'], $rows1['from_name']);
            }else{
                $this->from($rows1['from_email']);
            }
        }

        $rows2 = [
            'name'      =>  $title,
            'content'   =>  $content,
            'user_id'   =>  $user->id,
            'user_name' =>  $user->name,
            'user_email'=>  $user->email,
            'type'      =>  $type_id,
        ];
        $this->mail->insert($rows2);
        
        return $this->html($content);
    }
    
    public function renderHTML($__php, $__data)
    {
        $obLevel = ob_get_level();
        ob_start();
        extract($__data, EXTR_SKIP);
        try {
            eval('?' . '>' . $__php);
        } catch (Exception $e) {
            while (ob_get_level() > $obLevel) ob_end_clean();
            throw $e;
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) ob_end_clean();
            throw new FatalThrowableError($e);
        }
        return ob_get_clean();
    }
}