<?php
namespace Philip0514\Ark\Repositories\Dashboard;

use Sunra\PhpSimple\HtmlDomParser;
use Philip0514\Ark\Models\PageBlock;

class PageBlockRepository
{
    protected $model;

	function __construct(
		PageBlock $block
	){
		$this->block = $block;
	}

	public function parse($html, $json)
	{
        $dom = HtmlDomParser::str_get_html($html);
		$headerHtml = $footerHtml = $pageHtml = null;
		$headerJson = $footerJson = $pageJson = [];
		if($html){
			$headerDom = $dom->find('header', 0);
			$footerDom = $dom->find('footer', 0);
			
			if($headerDom){
				$headerHtml = $headerDom->outertext;
			}
			if($footerDom){
				$footerHtml = $footerDom->outertext;
			}
			$pageHtml = str_replace($headerHtml, '', $html);
			$pageHtml = str_replace($footerHtml, '', $pageHtml);
		}

		$json = $json ? json_decode($json, true) : null;
		for($i=0; $i<sizeof($json); $i++){
			if($json[$i]['type']=='header-root'){
				$headerJson = $json[$i];
			}elseif($json[$i]['type']=='footer-root'){
				$footerJson = $json[$i];
			}else{
				$pageJson[] = $json[$i];
			}
		}

		$this->block->where('key', 'header')->orderBy('id', 'asc')->first()->update([
			'html'	=>	$headerHtml,
			'json'	=>	$headerJson ? json_encode($headerJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
		]);

		$this->block->where('key', 'footer')->orderBy('id', 'asc')->first()->update([
			'html'	=>	$footerHtml,
			'json'	=>	$footerJson ? json_encode($footerJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
		]);

		return [
			'html'	=>	$pageHtml,
			'json'	=>	json_encode($pageJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
		];
	}

	public function merge($html, $json)
	{
		$header = $this->block->where('key', 'header')->orderBy('id', 'asc')->first();
		$footer = $this->block->where('key', 'footer')->orderBy('id', 'asc')->first();

		$html = $header.$html.$footer;

		$headerJson = json_decode($header['json'], true);
		$footerJson = json_decode($footer['json'], true);
		$pageJson = $json ? json_decode($json, true) : [];

		$json = [];
		if($headerJson){
			$json[] = $headerJson;
		}
		for($i=0; $i<sizeof($pageJson); $i++){
			$json[] = $pageJson[$i];
		}
		if($footerJson){
			$json[] = $footerJson;
		}

		return [
			'html'	=>	$html,
			'json'	=>	json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
		];
	}
}