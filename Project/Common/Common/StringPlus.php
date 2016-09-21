<?php
	/**
	 * Created by PhpStorm.
	 * User: Quasar
	 * Date: 2016/5/3
	 * Time: 14:35
	 */
	namespace Quasar;

	header('Content-type:text/html;charset=utf-8');

	class StringPlus{
		/**
		 * 生成加密密码
		 *
		 * @param string $pwd       明文密码
		 * @param string $interfere 干扰串
		 *
		 * @return string
		 */
		public function makePassword($pwd, $interfere = ''){
			$disturb = function($str){
				$tmp = $str;
				$tmp = str_replace('13', '@h', $tmp);
				$tmp = str_replace('31', 'v@3', $tmp);
				$tmp = str_replace('j7', '@bg2', $tmp);
				$tmp = str_replace('z4', 'i-', $tmp);
				$tmp = str_replace('ip', '@p', $tmp);
				$tmp = str_replace('h5', 'h_', $tmp);
				$tmp = str_replace('01', 'p_', $tmp);
				$tmp = str_replace('5h', '_5', $tmp);
				$tmp = str_replace('0c', 'b_', $tmp);
				$tmp = str_replace('f1', 'd_3', $tmp);
				$tmp = str_replace('82', '-0', $tmp);
				$tmp = str_replace('k3', 'h-', $tmp);
				$tmp = str_replace('b0', '1-', $tmp);
				$tmp = str_replace('9d', '@i', $tmp);
				$tmp = str_replace('a1', '4-', $tmp);
				$tmp = str_replace('j4', '-6z', $tmp);
				$tmp = str_replace('m3', '-m', $tmp);
				$tmp = str_replace('9b', '-9', $tmp);
				$tmp = str_replace('6h', '8-', $tmp);
				$tmp = str_replace('db', '7@', $tmp);
				$tmp = str_replace('x8', '@x', $tmp);
				$tmp = str_replace('e0', 'j6_', $tmp);
				$tmp = str_replace('5c', 'c@', $tmp);

				return $tmp;
			};
			$tmp  = md5("$pwd$interfere");
			$str1 = substr($tmp, 5, 16);
			$tmp  = md5(substr($tmp, 7, 13));
			$str2 = substr($tmp, 11, 16);
			$tmp  = md5(strrev($tmp));
			$str3 = substr($tmp, 13, 16);
			$tmp  = sha1($tmp);
			$str4 = substr($tmp, 17, 16);
			$tmp  = strtoupper("$str1$str2$str3$str4");

			return $tmp;
		}

		/**
		 * 获取字符的拼音码首字母
		 *
		 * @param string $words 输入的字符
		 * @param string $type  返回类型 指定是大写还是小写
		 * @param bool   $all   是否对整个字符串进行处理
		 *
		 * @return string
		 */
		public function makePinyinCode($words, $type = 'lower', $all = true){
			/**
			 * 截取字符串第一个字符
			 *
			 * @param string $string  需要被截取的字符串
			 * @param int    $start   开始位置
			 * @param int    $length  截取长度
			 * @param string $postfix 字符串截取后的后缀
			 *
			 * @return string
			 */
			$cutOutString = function ($string, $start, $length, $postfix = ''){
				$result = (mb_strlen($string, 'utf-8')<=$length) ? $string : mb_substr($string, $start, $length, 'utf-8');

				return $result.$postfix;
			};
			/**
			 * 获取字符串中文拼音首字母
			 *
			 * @param string $str 需要转换的字符串
			 *
			 * @return null|string
			 */
			$getLetter = function ($str){
				$firest_ord = ord($str);
				if($firest_ord>=48 && $firest_ord<=57){
					return $str;    //数字
				}
				elseif($firest_ord>=65 && $firest_ord<=90){
					return $str;    //大写字母
				}
				elseif($firest_ord>=97 && $firest_ord<=122){
					return $str;    //小写字母
				}
				//中文
				$s   = iconv("UTF-8", "gbk//IGNORE", $str);
				$asc = ord($s{0})*256+ord($s{1})-65536;
				if($asc>=-20319 and $asc<=-20284) return "A";
				if($asc>=-20283 and $asc<=-19776) return "B";
				if($asc>=-19775 and $asc<=-19219) return "C";
				if($asc>=-19218 and $asc<=-18711) return "D";
				if($asc>=-18710 and $asc<=-18527) return "E";
				if($asc>=-18526 and $asc<=-18240) return "F";
				if($asc>=-18239 and $asc<=-17923) return "G";
				if($asc>=-17922 and $asc<=-17418) return "H";
				if($asc>=-17417 and $asc<=-16475) return "J";
				if($asc>=-16474 and $asc<=-16213) return "K";
				if($asc>=-16212 and $asc<=-15641) return "L";
				if($asc>=-15640 and $asc<=-15166) return "M";
				if($asc>=-15165 and $asc<=-14923) return "N";
				if($asc>=-14922 and $asc<=-14915) return "O";
				if($asc>=-14914 and $asc<=-14631) return "P";
				if($asc>=-14630 and $asc<=-14150) return "Q";
				if($asc>=-14149 and $asc<=-14091) return "R";
				if($asc>=-14090 and $asc<=-13319) return "S";
				if($asc>=-13318 and $asc<=-12839) return "T";
				if($asc>=-12838 and $asc<=-12557) return "W";
				if($asc>=-12556 and $asc<=-11848) return "X";
				if($asc>=-11847 and $asc<=-11056) return "Y";
				if($asc>=-11055 and $asc<=-10247) return "Z";

				return $str;
			};
			$result    = '';
			if($all){
				$length = mb_strlen($words, 'utf-8');
				for($i = 0; $i<$length; $i++){
					$subStr = $cutOutString($words, $i, 1);
					$result .= $getLetter($subStr);
				}
			}
			else{
				$subStr = $cutOutString($words, 0, 1);
				$result = $getLetter($subStr);
			}
			switch(strtolower($type)){
				case 'lower':
				default:
					return strtolower($result);
				break;
				case 'upper':
					return $result;
				break;
			}
		}

		/**
		 * 生成GUID字符串
		 *
		 * @param string $namespace 干扰字符
		 * @param bool   $brace     是否输出花括号
		 *
		 * @return string
		 */
		public function makeGuid($namespace = '', $brace = true){
			$guid = '';
			$uid  = uniqid("", true);
			$data = $namespace;
			$data .= $_SERVER['REQUEST_TIME'];
			$data .= $_SERVER['HTTP_USER_AGENT'];
			$data .= $_SERVER['LOCAL_ADDR'];
			$data .= $_SERVER['LOCAL_PORT'];
			$data .= $_SERVER['REMOTE_ADDR'];
			$data .= $_SERVER['REMOTE_PORT'];
			$hash = strtoupper(hash('ripemd128', $uid.$guid.md5($data)));
			$guid = substr($hash, 0, 8).'-'.substr($hash, 8, 4).'-'.substr($hash, 12, 4).'-'.substr($hash, 16, 4).'-'.substr($hash, 20, 12);
			if($brace == true) $guid = '{'.$guid.'}';

			return $guid;
		}

		/**
		 * 创建随机字符串
		 * 传入参数$type为W(大小写字母)、W+(大写字母)、W-(小写字母)、N(数字)、C(特殊字符)的一定规则的组合字符串
		 *
		 * @param int    $length
		 * @param string $type
		 *
		 * @return string
		 */
		public function makeRandomString($length = 6, $type = 'NW'){
			$chars = [
				'abcdefghijklmnopqrstuvwxyz',
				'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'0123456789',
				'~!@#$%^&()[]{}_+=-;.,'
			];
			switch(strtoupper($type)){
				case 'N':
					$chars = $chars[2];
				break;
				case 'NW':
				case 'WN':
					$chars = $chars[0].$chars[1].$chars[2];
				break;
				case 'NC':
				case 'CN':
					$chars = $chars[2].$chars[3];
				break;
				case 'NW+':
				case 'W+N':
					$chars = $chars[1].$chars[2];
				break;
				case 'NW-':
				case 'W-N':
					$chars = $chars[0].$chars[2];
				break;
				case 'W+':
					$chars = $chars[1];
				break;
				case 'W-':
					$chars = $chars[0];
				break;
				case 'W':
					$chars = $chars[0].$chars[1];
				break;
				case 'W+C':
				case 'CW+':
					$chars = $chars[1].$chars[3];
				break;
				case 'W-C':
				case 'CW-':
					$chars = $chars[0].$chars[3];
				break;
				case 'WC':
				case 'CW':
					$chars = $chars[0].$chars[1].$chars[3];
				break;
				default:
					$chars = $chars[0].$chars[1].$chars[2];
				break;
			}
			$result = '';
			for($i = 0; $i<$length; $i++){
				// 这里提供两种字符获取方式
				// 第一种是使用substr 截取$chars中的任意一位字符；
				// 第二种是取字符数组$chars 的任意元素
				// $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
				$result .= $chars[mt_rand(0, strlen($chars)-1)];
			}

			return $result;
		}

		/**
		 * 过滤字符串（使用htmlspecialchars()、strip_tags()、addslashes()、quotemeta()）
		 *
		 * @param string $string 需要过滤的字符串
		 * @param int    $switch 过滤开关
		 *                       1：只执行htmlspecialchars()进行过滤HTML/XML/PHP标签
		 *                       2：只执行strip_tags()进行HTML标签转义
		 *                       3：执行htmlspecialchars()和strip_tags()
		 *                       4：只执行addslashes()添加反斜线
		 *                       5：默认只执行htmlspecialchars()和addslashes()
		 *                       6：只执行strip_tags()和addslashes()
		 *                       7：执行htmlspecialchars()、strip_tags()和addslashes()
		 *                       8：只执行quotemeta()添加反斜线
		 *                       9：只执行htmlspecialchars()和quotemeta()
		 *                       10：只执行strip_tags()和quotemeta()
		 *                       11：不执行addslashes()
		 *                       12：只执行quotemeta()和addslashes()
		 *                       13：不执行strip_tags()
		 *                       14：不执行htmlspecialchars()
		 *                       15：执行所有过滤函数
		 * @param bool   $trim   是否去掉两端的空格
		 *
		 * @return string 返回过滤后的字符串
		 */
		public function filterString($string, $switch = 5, $trim = true){
			if($trim) $string = trim($string);
			switch($switch){
				case 1:
					$string = htmlspecialchars($string);
				break;
				case 2:
					$string = strip_tags($string);
				break;
				case 3:
					$string = htmlspecialchars($string);
					$string = strip_tags($string);
				break;
				case 4:
					$string = addslashes($string);
				break;
				case 5:
				default:
					$string = htmlspecialchars($string);
					$string = addslashes($string);
				break;
				case 6:
					$string = strip_tags($string);
					$string = addslashes($string);
				break;
				case 7:
					$string = htmlspecialchars($string);
					$string = strip_tags($string);
					$string = addslashes($string);
				break;
				case 8:
					$string = quotemeta($string);
				break;
				case 9:
					$string = htmlspecialchars($string);
					$string = quotemeta($string);
				break;
				case 10:
					$string = strip_tags($string);
					$string = quotemeta($string);
				break;
				case 11:
					$string = htmlspecialchars($string);
					$string = strip_tags($string);
					$string = quotemeta($string);
				break;
				case 12:
					$string = addslashes($string);
					$string = quotemeta($string);
				break;
				case 13:
					$string = htmlspecialchars($string);
					$string = addslashes($string);
					$string = quotemeta($string);
				break;
				case 14:
					$string = strip_tags($string);
					$string = addslashes($string);
					$string = quotemeta($string);
				break;
				case 15:
					$string = htmlspecialchars($string);
					$string = strip_tags($string);
					$string = addslashes($string);
					$string = quotemeta($string);
				break;
			}

			return $string;
		}

		public function restoreString($string){
			$string = htmlspecialchars_decode($string);
			$string = stripslashes($string);

			return $string;
		}
	}
