/**
 *
 * Base64 encode / decode
 *
 * @author haitao.tu
 * @date 2010-04-26
 * @email tuhaitao@foxmail.com
 *
 */
(function(){
	/**
	 * Base64加密
	 *
	 * @returns string
	 */
	String.prototype.base64Encode = function(){
		var _thisString = this.toString();
		// private property
		var _keyStr     = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
		// private method for UTF-8 encoding
		var _utf8Encode = function(string){
			string      = string.replace(/\r\n/g, "\n");
			var utf_str = "";
			for(var n = 0; n<string.length; n++){
				var c = string.charCodeAt(n);
				if(c<128){
					utf_str += String.fromCharCode(c);
				}else if((c>127) && (c<2048)){
					utf_str += String.fromCharCode((c >> 6)|192);
					utf_str += String.fromCharCode((c&63)|128);
				}else{
					utf_str += String.fromCharCode((c >> 12)|224);
					utf_str += String.fromCharCode(((c >> 6)&63)|128);
					utf_str += String.fromCharCode((c&63)|128);
				}
			}
			return utf_str;
		};
		var output      = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i           = 0;
		_thisString     = _utf8Encode(_thisString);
		while(i<_thisString.length){
			chr1 = _thisString.charCodeAt(i++);
			chr2 = _thisString.charCodeAt(i++);
			chr3 = _thisString.charCodeAt(i++);
			enc1 = chr1 >> 2;
			enc2 = ((chr1&3) << 4)|(chr2 >> 4);
			enc3 = ((chr2&15) << 2)|(chr3 >> 6);
			enc4 = chr3&63;
			if(isNaN(chr2)){
				enc3 = enc4 = 64;
			}else if(isNaN(chr3)){
				enc4 = 64;
			}
			output = output+
				_keyStr.charAt(enc1)+_keyStr.charAt(enc2)+
				_keyStr.charAt(enc3)+_keyStr.charAt(enc4);
		}
		return output;
	};
	/**
	 * Base64解密
	 *
	 * @returns string
	 */
	String.prototype.base64Decode = function(){
		var _thisString = this.toString();
		// private property
		var _keyStr     = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
		// private method for UTF-8 decoding
		var _utf8Decode = function(uft_str){
			var string = "";
			var i      = 0;
			var c      = 0, c2 = 0, c3 = 0;
			while(i<uft_str.length){
				c = uft_str.charCodeAt(i);
				if(c<128){
					string += String.fromCharCode(c);
					i++;
				}else if((c>191) && (c<224)){
					c2 = uft_str.charCodeAt(i+1);
					string += String.fromCharCode(((c&31) << 6)|(c2&63));
					i += 2;
				}else{
					c2 = uft_str.charCodeAt(i+1);
					c3 = uft_str.charCodeAt(i+2);
					string += String.fromCharCode(((c&15) << 12)|((c2&63) << 6)|(c3&63));
					i += 3;
				}
			}
			return string;
		};
		var output      = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i           = 0;
		_thisString     = _thisString.replace(/[^A-Za-z0-9\+\/=]/g, "");
		while(i<_thisString.length){
			enc1   = _keyStr.indexOf(_thisString.charAt(i++));
			enc2   = _keyStr.indexOf(_thisString.charAt(i++));
			enc3   = _keyStr.indexOf(_thisString.charAt(i++));
			enc4   = _keyStr.indexOf(_thisString.charAt(i++));
			chr1   = (enc1 << 2)|(enc2 >> 4);
			chr2   = ((enc2&15) << 4)|(enc3 >> 2);
			chr3   = ((enc3&3) << 6)|enc4;
			output = output+String.fromCharCode(chr1);
			if(enc3 != 64){
				output = output+String.fromCharCode(chr2);
			}
			if(enc4 != 64){
				output = output+String.fromCharCode(chr3);
			}
		}
		output = _utf8Decode(output);
		return output;
	};
})();
