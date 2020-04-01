<?php
namespace Philip0514\Ark;


class Media {

    public function integrate($rows1, $size='square')
    {
		$media_id = [];
		for($i=0; $i<sizeof($rows1); $i++){
            $media_id[] = $rows1[$i]['id'];

            $name = $rows1[$i]['name'];
            $month = date('Ym', strtotime($rows1[$i]['created_at']));

			$rows1[$i]['path'] = sprintf('%s%s/%s/%s/%s', config('ark.media.root'), config('ark.media.upload'), $size, $month, $name);
        }

		if(!sizeof($media_id)){
			return [
				'id'	=>	'',
				'data'	=>	[],
			];
		}

		return [
			'id'	=>	implode(',', $media_id),
			'data'	=>	$rows1
		];
    }
}