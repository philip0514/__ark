<?php

namespace Philip0514\Ark\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\Debug\Exception\FatalThrowableError;

use Philip0514\Ark\Models\MailTemplate;

class Mailer extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->template = new MailTemplate();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $type = $this->data['type'];
        $data = $this->data['data'];

        $type_id = null;
        switch($type)
        {
            case 'registerPassword':
                $type_id = 2;
            break;
            case 'registerFacebook':
                $type_id = 3;
            break;
            case 'registerGoogle':
                $type_id = 4;
            break;
        }

        if(!$type_id){
            return null;
        }

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

        $blade = htmlspecialchars_decode($rows1['content']);
        $php = Blade::compileString($blade);
        $html = $this->renderHTML($php, $data);
        $this->subject($rows1['title']);
        return $this->html($html);
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