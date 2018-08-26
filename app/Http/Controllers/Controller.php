<?php namespace App\Http\Controllers;

//use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Picture;

abstract class Controller extends BaseController {

//	use DispatchesCommands, ValidatesRequests;

	// 获取图片的url
	public function getPicUrl($id)
	{
		$pic = Picture::find($id);
		return $pic['path'];
	}

	// 上传图片
	public function addPic($file)
	{
		$pub_dir = public_path();
		$path = '/default/app/img/';
		$date = date('Y-m-d');
		$path = $path.$date.'/';
		$upload_dir = $pub_dir.$path;
		if(!is_dir($upload_dir))
		{
			$result = mkdir($upload_dir, 0777);
			if(!$result)
			{
				throw new Exception("目录权限问题，请检查", 1);
			}
		}
		$user = \Session::get('currentUser');

		$name = time().$user['id'];
		$ext = $file->guessClientExtension();
		$name = md5($name).'.'.$ext;
		if($file->isValid())
		{
			$file->move($upload_dir, $name);
		}
		$pic = Picture::create(array('path'=>$path.$name, 'status'=>'1'));
		return $pic;
	}

	private function get_pic_ext($picname)
	{
		$ext = substr($picname, strpos($picname, '.')+1);
		return $ext;
	}

	public function getAllProvinces()
	{
		$province = \DB::select("SELECT * FROM `province`");
		return $province;
	}

	public function getAllCities($province_id)
	{
		$cities = \DB::select("SELECT * FROM `city` WHERE `father` = $province_id ");
		return $cities;
	}

	public function getAllAreas($city_id)
	{
		$areas = \DB::select("SELECT * FROM `area` WHERE `father` = $city_id");
		return $areas;
	}

	public function getArea($id)
	{
		$area = \DB::select('SELECT * FROM `area` WHERE `areaID` = :areaID LIMIT 1', ['areaID'=>$id]);
		return $area[0];
	}

	public function getCity($id)
	{
		$city = \DB::select('SELECT * FROM `city` WHERE `cityID` = :cityID ', ['cityID'=> $id]);
		return $city[0];
	}

	public function getProvince($id)
	{
		$province = \DB::select('SELECT * FROM `province` WHERE `provinceID`=:provinceID', ['provinceID'=> $id]);
		return $province[0];
	}

	public function getMerchandiseType($id)
	{
		$type = \DB::select('SELECT * FROM `merchandise_type` WHERE `id`=:id', ['id'=> $id]);
		return $type['0'];
	}

	public function getMerchandiseStatus($id)
	{
		$status = \DB::select('SELECT * FROM `merchandise_status` WHERE `id` =:id', ['id'=>$id]);
		return $status['0'];
	}

	public function getMerchandiseShippiingMethod($id)
	{
		$shipping_method = \DB::select('SELECT * FROM `merchandise_shipping_method` WHERE `id`=:id', ['id'=>$id]);
		return $shipping_method[0];
	}

	public function getAllMerchandiseType()
	{
		$merchandise_type = \DB::select('SELECT * FROM `merchandise_type`');
		return $merchandise_type;
	}

	public function getAllMerchandiseSM()
	{
		$m_shipping_method = \DB::select('SELECT * FROM `merchandise_shipping_method`');
		return $m_shipping_method;
	}

	public function getVehicleBodyType($id)
	{
		$type = \DB::select('SELECT * FROM `vehicle_body_type` WHERE `id` =:id', ['id'=> $id]);
		return $type['0'];
	}

	public function getVehicleType($id)
	{
		$type = \DB::select('SELECT * FROM `vehicle_type` WHERE `id` =:id', ['id'=>$id]);
		return $type['0'];
	}

	public function getAllVehicleType()
	{
		$vehicleType = \DB::select('SELECT * FROM `vehicle_type` ORDER BY id ASC');
		return $vehicleType;
	}

	public function getAllVehicleBodyType()
	{
		$vbodyType = \DB::select('SELECT * FROM `vehicle_body_type` ORDER BY id ASC');
		return $vbodyType;
	}

	// 地址级联选选项
	public function areaSelectPlugin()
	{
		$province = $this->getAllProvinces();
		$default_pro = $province['0'];
		$city = $this->getAllCities($default_pro->provinceID);
		$default_city = $city['0'];
		$area = $this->getAllAreas($default_city->id);
		$select['province'] = $province;
		$select['city'] = $city;
		$select['area'] = $area;
		\View::share('area', $select);
	}

	// 发送电子邮件
	public function sendMail($to, $subject, $message)
	{
		\Mail::send('default._shared.mail',['key'=> 'val'],function($message){
			$message->to('rchangchun@126.com', 'John Smith')->subject('Welcome!');
		});
	}




}
