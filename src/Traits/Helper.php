<?php
namespace Philip0514\Ark\Traits;

use Symfony\Component\Debug\Exception\FatalThrowableError;
use Blade;

/**
 * Trait Helpers
 *
 * @package App\Traits
 */
trait Helper
{
    protected function skip_empty($rows1)
    {
        $rows2 = null;
        for($i=0; $i<sizeof($rows1); $i++){
            if($rows1[$i]){
                $rows2[] = trim($rows1[$i]);
            }
        }
        
        return $rows2;
    }

    public function checkTrashed()
    {
        $exist = isset($this->forceDeleting);
        if($exist){
            return $this->withTrashed();
        }

        return $this->query();
    }

	public function mediaPath($name, $folder='square')
	{
		list($time, $t) = explode('-', $name);
        $month = date('Ym', $time);

        if(config('ark.media.s3.active')){
            $root = config('ark.media.s3.root');
        }else{
            $root = '//'.request()->getHost().config('ark.media.root');
        }
        $path = sprintf('%s%s/%s/%s/%s', $root, config('ark.media.upload'), $folder, $month, $name);

        return $path;
    }

    public function bladeHtml($html, $data=[])
    {
		$html = html_entity_decode($html, ENT_QUOTES);
        $php = Blade::compileString($html);
		return $this->renderHtml($php, $data);
    }

    private function renderHtml($__php, $__data=[])
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