<?php
namespace Philip0514\Ark\Repositories;

use Philip0514\Ark\Repositories\Repository;
use Storage;
use Auth;
use Intervention\Image\ImageManagerStatic as Image;

use Philip0514\Ark\Models\Media as Model;

//trait
use Philip0514\Ark\Traits\Helper;

class MediaRepository extends Repository
{
	use Helper;

    protected $model;

	function __construct(
		Model $model
	){
		parent::__construct();
		$this->model = $model;
	}

	public function upload($request)
	{
		$file = $request->file('file', null);

		$month = date('Ym');

		if(!$file){
			return 0;
		}
		$administrator = Auth::guard('admin')->user();
		$administrator_id = $administrator->id;

		$public = Storage::disk('public');
		$dimensions = config('ark.media.dimensions');
		$month = date('Ym');
		$ext = $file->getClientOriginalExtension();
		$name = sprintf('%s-%s.%s', time(), uniqid(), $ext);
		$path = sprintf('%s/original/%s/%s', config('ark.media.upload'), $month, $name);
		$original_name = $file->getClientOriginalName();
		$size = $file->getSize();
		$type = $file->getClientMimeType();
		$is_image = $this->is_image($type);
		$height = Image::make($file)->height();
		$width = Image::make($file)->width();
		$public->put($path, file_get_contents($file));

		if(config('ark.media.s3.active')){
			$s3 = Storage::disk('s3');
			$s3->put($path, file_get_contents($file));
		}

		//去除副檔名
		$original_name = str_replace('.'.$ext, '', $original_name);
		
		$this->resize($name, $width, $height);

		$id = $this->model->insertGetId([
			'administrator_id'	=>	$administrator_id,
			'name'				=>	$name,
			'orig_name'			=>	$file->getClientOriginalName(),
			'ext'				=>	$ext,
			'title'				=>	$original_name,
			'file_path'			=>	$path,
			'file_type'			=>	$type,
			'file_size'			=>	$size,
			'display'			=>	1,
			'image_width'		=>	$width,
			'image_height'		=>	$height,
			'is_image'			=>	$is_image,
			'created_by'		=>	$administrator_id,
			'updated_by'		=>	$administrator_id,
		]);

		return [
			'id'			=>	$id,
			'name'			=>	$name,
			'title'			=>	$original_name,
			'path'			=>	sprintf('%s%s/square/%s/%s', config('ark.media.root'), config('ark.media.upload'), $month, $name),
			'created_at'	=>	date('Y-m-d H:i:s', time()),
		];
	}

    public function single($id)
    {
		$rows1 = $this->model
		->checkTrashed()
		->with(['tags' =>  function($query){
			$query->select('*', 'name as text');
		}])
		->find($id);

        if($rows1){
            return $rows1->toArray();
        }
        return null;
    }

    public function update($data)
    {
		$data = $this->_update($data);
        $id = $data['id'];
		$tag = $data['tag'];
		unset($data['tag']);
        $deleted = isset($data['deleted']) ? $data['deleted'] : null;
        unset($data['deleted']);

        $data['deleted_by'] = null;
        if($deleted){
            $data['display'] = 0;
            $data['deleted_by'] = $data['updated_by'];
        }else{
            $this->restore($id);
        }

		$cropper_data = json_decode($data['crop_data'], true);
		$custom_crop = $data['custom_crop'];

		$rows1 = $this->model->checkTrashed()->find($id);
		$name = $rows1->name;
		$width = $rows1->image_width;
		$height = $rows1->image_height;

		//裁切
		$this->resize($name, $width, $height, $cropper_data, $custom_crop);

        $this->model
            ->where('id', $id)
			->update($data);

		//tag
		$rows1->tags()->sync($tag);

        if($deleted){
            $this->delete($id);
        }

		return [
			'id'	=>	$rows1['id'],
			'path'	=>	$this->mediaPath($name),
		];
	}

    protected function actionDeleteForce($id)
    {
		$public = Storage::disk('public');
		$s3 = Storage::disk('s3');
		$rows1 = $this->model->checkTrashed()->whereIn('id', $id)->get();
		$dimensions = config('ark.media.dimensions');

		$files = [];
		for($i=0; $i<sizeof($rows1); $i++){
			$name = $rows1[$i]->name;
			list($time, $t) = explode('-', $name);
			$month = date('Ym', $time);

			$files[] = sprintf('%s/original/%s/%s', config('ark.media.upload'), $month, $name);
			foreach($dimensions as $dimension => $value){
				$files[] = sprintf('%s/%s/%s/%s', config('ark.media.upload'), $value['folder'], $month, $name);
			}

			$this->model->detachAll($rows1[$i]);
		}

		$public->delete($files);
		if(config('ark.media.s3.active')){
			$s3->delete($files);
		}

		//刪除資料
		$this->model->whereIn('id', $id)->forceDelete();
    }

	/**
	 * 這邊要先判斷圖片尺寸 是橫式的還是直式的
	 * 若是橫式 則以高度為主 後面在裁切寬度
	 * 若是直式 則反之
	 */
	private function resize($name, $width=0, $height=0, $cropper_data=[], $custom_crop=0)
	{
		if(config('ark.media.s3.active')){
			$s3 = Storage::disk('s3');
		}
		$public = Storage::disk('public');
		$dimensions = config('ark.media.dimensions');
		list($time, $t) = explode('-', $name);
		$month = date('Ym', $time);

		//圖片樣式：橫圖、直圖、正方圖
		if($width > $height){
			//橫式 landscape 高度固定 寬度變動
			$image_shape = 'landscape';
		}else if($width < $height){
			//直式 portrait 寬度固定 高度變動
			$image_shape = 'portrait';
		}else{
			//正方形 square
			$image_shape = 'square';
		}

		$i=0;
		foreach($dimensions as $dimension => $value){
			//複製並裁切
			$original_name = sprintf('%s/original/%s/%s', config('ark.media.upload'), $month, $name);
			$file_name = sprintf('%s/%s/%s/%s', config('ark.media.upload'), $value['folder'], $month, $name);

			if(!$public->exists($file_name)){
				if(config('ark.media.s3.active')){
					$public->put($original_name, $s3->get($original_name));
				}
				$public->copy($original_name, $file_name);
			}

			$image = Image::make($public->get($original_name));

			//若「自訂裁切」為false 則以第一個的裁切為標準
			if(!$custom_crop && $value['custom-crop']==1 && isset($cropper_data[0])){
				$cropper_data[$i] = $cropper_data[0];
			}

			$crop = [
				'width'		=> isset($cropper_data[$i]) ? (int)$cropper_data[$i]['width'] : $width,
				'height' 	=> isset($cropper_data[$i]) ? (int)$cropper_data[$i]['height'] : $height,
				'x' 		=> isset($cropper_data[$i]) ? (int)$cropper_data[$i]['x'] : 0,
				'y' 		=> isset($cropper_data[$i]) ? (int)$cropper_data[$i]['y'] : 0,
			];

			$resize = [
				'width'		=>	$value['width'],
				'height'	=>	$value['height'],
			];

			/*
				w1/h1 = w2/h2
				w2 = w1/h1*h2

				h1/w1 = h2/w2
				h2 = h1/w1*w2
			*/
			switch($image_shape){
				//原圖為橫式 landscape 高度固定 寬度變動
				case 'landscape':

					switch($value['shape']){
						//等比
						case 'isometric':
							$image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
							$image->resize($resize['width'], null, function($constraint){
								$constraint->aspectRatio();
								$constraint->upsize();
							});
						break;

						//裁切成正方形
						case 'square':
							$crop['x'] = round($crop['x'] + ($crop['width']-$crop['height'])/2);
							$crop['width'] = $crop['height'];
							$image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
							$image->resize($resize['width'], $resize['height']);
						break;

						//指定尺寸的橫圖
						case 'landscape':
							$crop['height'] = round($resize['height'] / $resize['width'] * $crop['width']);
							$crop['y'] = $crop['y'] ? $crop['y'] : round(($height-$crop['height'])/2);
							$image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
							$image->resize($resize['width'], $resize['height']);
						break;

						//指定尺寸的直圖
						case 'portrait':
							$crop['width'] = round($resize['width'] / $resize['height'] * $crop['height']);
							$crop['x'] = $crop['x'] ? $crop['x'] : round(($width-$crop['width'])/2);
							$image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
							$image->resize($resize['width'], $resize['height']);
						break;
					}
				break;
				//原圖為直式 portrait 寬度固定 高度變動
				case 'portrait':

					switch($value['shape']){
						//等比
						case 'isometric':
							$image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
							$image->resize($resize['width'], null, function($constraint){
								$constraint->aspectRatio();
								$constraint->upsize();
							});
						break;

						//裁切成正方形
						case 'square':
							$crop['y'] = round($crop['y'] + ($crop['height']-$crop['width'])/2);
							$crop['height'] = $crop['width'];
							$image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
							$image->resize($resize['width'], $resize['height']);
						break;

						//指定尺寸的橫圖
						case 'landscape':
							$crop['height'] = round($resize['height'] / $resize['width'] * $crop['width']);
							$crop['y'] = $crop['y'] ? $crop['y'] : round(($height-$crop['height'])/2);
							$image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
							$image->resize($resize['width'], $resize['height']);
						break;

						//指定尺寸的直圖
						case 'portrait':
							$crop['width'] = round($resize['width'] / $resize['height'] * $crop['height']);
							$crop['x'] = $crop['x'] ? $crop['x'] : round(($width-$crop['width'])/2);
							$image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
							$image->resize($resize['width'], $resize['height']);
						break;
					}
				break;
				//原圖為正方形 square
				case 'square':
					
					switch($value['shape']){
						//等比
						case 'isometric':
							$image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
							$image->resize($resize['width'], null, function($constraint){
								$constraint->aspectRatio();
								$constraint->upsize();
							});
						break;

						//裁切成正方形
						case 'square':
							$crop['x'] = round($crop['x'] + ($crop['width']-$crop['height'])/2);
							$crop['width'] = $crop['height'];
							$image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
							$image->resize($resize['width'], $resize['height']);
						break;

						//指定尺寸的橫圖
						case 'landscape':
							$crop['height'] = round($resize['height'] / $resize['width'] * $crop['width']);
							$crop['y'] = $crop['y'] ? $crop['y'] : round(($height-$crop['height'])/2);
							$image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
							$image->resize($resize['width'], $resize['height']);
						break;

						//指定尺寸的直圖
						case 'portrait':
							$crop['width'] = round($resize['width'] / $resize['height'] * $crop['height']);
							$crop['x'] = $crop['x'] ? $crop['x'] : round(($width-$crop['width'])/2);
							$image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
							$image->resize($resize['width'], $resize['height']);
						break;
					}
				break;
			}
			$image->save($public->getDriver()->getAdapter()->getPathPrefix().$file_name);

			if(config('ark.media.s3.active')){
				$s3->put($file_name, $public->get($file_name));
				$public->delete($file_name);
			}
			$i++;
		}

		if(config('ark.media.s3.active')){
			$public->delete($original_name);
		}
	}

	private function is_image($file_type)
	{
		$png_mimes  = ['image/x-png'];
		$jpeg_mimes = ['image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg'];

		if (in_array($file_type, $png_mimes)){
			$file_type = 'image/png';
		}elseif (in_array($file_type, $jpeg_mimes)){
			$file_type = 'image/jpeg';
		}

		$img_mimes = ['image/gif',	'image/jpeg', 'image/png'];

		return in_array($file_type, $img_mimes, TRUE);
	}

	public function data($page, $limit, $request_time, $skip=null, $search=null)
	{
		$start = $limit*($page-1);

		$result = $this->model->where('created_at', '<', date('Y-m-d H:i:s', $request_time));
		if($skip){
			$result = $result->whereNotIn('id', explode(',', $skip));
		}

		if($search){
			$result = $result->where('title', 'like', '%'.$search.'%');
		}

		$result = $result
			->orderBy('id', 'desc')
			->offset($start)
			->limit($limit)
			->get()
			->toArray();

		return $result;
	}
}