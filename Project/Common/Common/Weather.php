<?php
	/*
	 * 更新日志
	 *
	 * Version 1.00 2016-12-29 15:15
	 * 初始版本
	 */
	namespace Quasar\ExternalInterface;

	use Quasar\Utility\Curl;
	use Quasar\Utility\Type;
	use Quasar\Utility\TypeError;

	/**
	 * 封装了天气相关的获取方法<br>
	 * <br>
	 * CreateTime: 2016-12-29 15:15<br>
	 * ModifyTime: 2016-12-29 15:15<br>
	 *
	 * @author  Quasar (lelouchcctony@163.com)
	 * @see     Curl
	 * @see     Type
	 * @version 1.00
	 */
	class Weather implements ChinaWeatherNativeLibrary, ETouchWeatherNativeLibrary{
		/** @var string 城市代码文件路径 */
		private $_cityPath = '';

		/**
		 * Weather constructor.
		 *
		 * @param null|string $city_path 城市代码文件路径
		 */
		public function __construct($city_path = null){
			$this->setCityListFilePath($city_path);
		}

		/**
		 * 获取实时省份天气情况
		 *
		 * @return array
		 */
		public function getProvinceWeather(){
			$result = $this->_getRequest('http://flash.weather.com.cn/wmaps/xml/china.xml', 'xml');

			return ['status' => true, 'data' => $result, 'message' => '获取成功'];
		}

		/**
		 * 获取城市实时天气情况
		 *
		 * @param string $city_name 城市拼音码
		 *
		 * @return array
		 */
		public function getCityWeather($city_name){
			$result = $this->_getRequest("http://flash.weather.com.cn/wmaps/xml/$city_name.xml", 'xml');
			if(isset($result['_status_']) && !$result['_status_']) return [
				'status'  => false,
				'message' => "找不到该城市($city_name)信息"
			];

			return ['status' => true, 'data' => $result, 'message' => '获取成功'];
		}

		/**
		 * 获取城市实时天气详情
		 *
		 * @param string $city_code 城市代码
		 *
		 * @return array
		 */
		public function getDetail($city_code){
			$result1 = $this->_getRequest("http://www.weather.com.cn/data/sk/$city_code.html", 'json');
			$result2 = $this->_getRequest("http://www.weather.com.cn/data/cityinfo/$city_code.html", 'json');
			if(!isset($result1['_status_'])) $result1 = $result1['weatherinfo'];
			if(!isset($result1['_status_'])) $result2 = $result2['weatherinfo'];
			if(!isset($result1['_status_']) && !isset($result2['_status_'])) $result = [
				'status'  => true,
				'message' => '获取成功',
				'data'    => array_merge($result1, $result2)
			];
			else{
				if(!isset($result1['_status_'])) $result = [
					'status'  => true,
					'message' => '获取成功',
					'data'    => $result1
				];
				elseif(!isset($result2['_status_'])) $result = [
					'status'  => true,
					'message' => '获取成功',
					'data'    => $result2
				];
				else $result = ['status' => false, 'message' => '获取失败'];
			}

			return $result;
		}

		/**
		 * 获取天气预报信息
		 *
		 * @param string $city_name 城市中文名或城市代码
		 *
		 * @return array
		 */
		public function getForecast($city_name){
			$refactoringIndex = function ($data, $first = 0) use (&$refactoringIndex){
				$list     = [
					'origin' => [
						'shidu',
						'fengli',
						'fengxiang',
						'wendu',
						'date_1',
						'high_1',
						'low_1',
						'day_1',
						'type_1',
						'fx_1',
						'fl_1',
						'alarm_details',
						'night_1',
					],
					'change' => [
						'humidity',
						'windForce',
						'windDirection',
						'temperature',
						'date',
						'high',
						'low',
						'day',
						'type',
						'windDirection',
						'windForce',
						'alarmDetail',
						'night',
					]
				];
				$new_list = [];
				foreach($data as $key => $val){
					if(is_array($val)) $val = $refactoringIndex($val);
					if(in_array($key, $list['origin']) && !is_numeric($key)){
						$temp = $val;
						unset($data[$key]);
						$change_index                             = array_search($key, $list['origin']);
						$new_list[$list['change'][$change_index]] = $temp;
					}
					else $new_list[$key] = $val;
				}
				$data = $new_list;
				if($first == 1){
					// 重构指数索引数据
					$index = $data['zhishus']['zhishu'];
					unset($data['zhishus']);
					$data['index'] = $index;
					// 重构预报索引数据
					$forecast = $data['forecast']['weather'];
					unset($data['forecast']);
					$data['forecast'] = $forecast;
				}

				return $data;
			};
			$result           = $this->_getRequest("http://wthrcdn.etouch.cn/WeatherApi?citykey=$city_name", 'xml', ['gzip' => true]);
			if(!isset($result['city'])) return ['status' => false, 'message' => "找不到该城市($city_name)的信息"];
			$result = $refactoringIndex($result, 1);

			return ['status' => true, 'message' => '获取成功', 'data' => $result];
		}

		/**
		 * 获取轻量的天气预报信息
		 *
		 * @param string $city_name 城市中文名或城市代码
		 *
		 * @return array
		 */
		public function getForecastMini($city_name){
			$refactoringIndex = function ($data) use (&$refactoringIndex){
				$list     = [
					'origin' => [
						'fengli',
						'fengxiang',
						'wendu',
						'fx',
						'fl',
						'ganmao'
					],
					'change' => [
						'windForce',
						'windDirection',
						'temperature',
						'windDirection',
						'windForce',
						'cold'
					]
				];
				$new_list = [];
				foreach($data as $key => $val){
					if(is_array($val)) $val = $refactoringIndex($val);
					if(in_array($key, $list['origin']) && !is_numeric($key)){
						$temp = $val;
						unset($data[$key]);
						$change_index                             = array_search($key, $list['origin']);
						$new_list[$list['change'][$change_index]] = $temp;
					}
					else $new_list[$key] = $val;
				}
				$data = $new_list;

				return $data;
			};
			$result           = $this->_getRequest("http://wthrcdn.etouch.cn/weather_mini?citykey=$city_name", 'json', ['gzip' => true]);
			if($result['status'] == '1000'){
				$result = $result['data'];
				$result = $refactoringIndex($result);
			}
			else return ['status' => false, 'message' => "找不到该城市($city_name)的信息"];

			return ['status' => true, 'message' => '获取成功', 'data' => $result];
		}

		/**
		 * 设置城市代码文件路径
		 *
		 * @param null|string $city_path 文件路径
		 */
		public function setCityListFilePath($city_path = null){
			$original_path   = dirname(__FILE__."../")."/WeatherCityCode.json";
			$this->_cityPath = realpath($city_path === null ? $original_path : $city_path);
		}

		/**
		 * 获取城市代码列表
		 *
		 * @return array
		 */
		public function getCityList(){
			$city_list_content = @file_get_contents($this->_cityPath);
			$city_list_json    = json_decode($city_list_content);
			if(!$city_list_json) return ['status' => false, 'message' => '找不到城市数据'];
			$result = [];
			foreach($city_list_json as $val) $result[] = ['code' => $val->code, 'name' => $val->name];

			return $result;
		}

		/**
		 * 发送get请求
		 *
		 * @param string $api_url 接口地址
		 * @param string $type    返回数据类型 可选值为 ('xml', 'json')
		 * @param array  $option  设置信息
		 *
		 * @return array 请求结果
		 */
		private function _getRequest($api_url, $type, $option = []){
			$curl_obj   = new Curl();
			$type_obj   = new Type();
			$native_str = $curl_obj->get($api_url);
			if(isset($option['gzip']) && $option['gzip']) $native_str = gzdecode($native_str);
			switch($type){
				case 'xml':
					try{
						$result = $type_obj->parseXmlToArray($native_str);
					}catch(TypeError $error){
						return ['_status_' => false];
					}
				break;
				case 'json':
					$result = json_decode($native_str);
					$result = $type_obj->parseObjectToArray($result);
					if(!$result) return ['_status_' => false];
				break;
				default:
					$result = $native_str;
				break;
			}

			return $result;
		}
	}

	interface ChinaWeatherNativeLibrary{
		public function getProvinceWeather();

		public function getCityWeather($city_name);

		public function getDetail($city_code);
	}

	interface ETouchWeatherNativeLibrary{
		public function getForecastMini($city_name);

		public function getForecast($city_name);
	}