<?php

namespace Moka;

/**
 * Class Aes
 * @author Lucifer from Moka
 */
class Aes
{
	const KEY_SIZE = 32;
	const BLOCK_SIZE = 32;

	/**
	 * Aes加密
	 * @param String $key        32位钥匙
	 * @param Complex $plaintext 明文
	 * @param String $flag       标识符 mokaid
	 * @return String 密文
	 */
	public static  function encrypt($plaintext, String $key, $flag)
	{
		if (strlen($key) != self::KEY_SIZE) {
			return 'key长度必须满足32位';
		}

		if (is_array($plaintext)) {
			$plaintext = json_encode($plaintext);
		}

		$key = base64_decode($key . "=");
		$random = self::getRandomStr();
		$pack_plaintext = $random . pack("N", strlen($plaintext)) . $plaintext . $flag;
		$iv = substr($key, 0, 16);
		$padding_plaintext = self::encode($pack_plaintext);
		$encrypted = openssl_encrypt($padding_plaintext, 'AES-256-CBC', $key, OPENSSL_ZERO_PADDING, $iv);
		return $encrypted;
	}

	/**
	 * Aes解密 
	 * @param Complex $encrypted 密文
	 * @return String 明文
	 */
	public static function decrypt($encrypted, String $key, $flag)
	{
		if (strlen($key) != self::KEY_SIZE) {
			return 'key长度必须满足32位';
		}

		$key = base64_decode($key . "=");
		$iv = substr($key, 0, 16);
		$decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_ZERO_PADDING, $iv);
		$result = self::decode($decrypted);
		if (strlen($result) < 16) return "";
		$content = substr($result, 16, strlen($result));
		$len_list = unpack("N", substr($content, 0, 4));
		$len = $len_list[1];
		$parse_flag = substr($content, $len + 4);
		if ($parse_flag != $flag) throw  new \Exception("aes flag error.");
		$plaintext = substr($content, 4, $len);
		return $plaintext;
	}

	/**
	 * 获取随机字符串
	 */
	private static function getRandomStr()
	{
		$str = "";
		$pool = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($pool) - 1;
		for ($i = 0; $i < 16; $i++) {
			$str .= $pool[mt_rand(0, $max)];
		}
		return $str;
	}

	private static function encode($text)
	{
		$text_length = strlen($text);
		$amount_to_pad = self::BLOCK_SIZE - ($text_length % self::BLOCK_SIZE);
		if ($amount_to_pad == 0) {
			$amount_to_pad = self::BLOCK_SIZE;
		}
		$pad_chr = chr($amount_to_pad);
		$tmp = "";
		for ($index = 0; $index < $amount_to_pad; $index++) {
			$tmp .= $pad_chr;
		}
		return $text . $tmp;
	}

	private static function decode($text)
	{

		$pad = ord(substr($text, -1));
		if ($pad < 1 || $pad > self::BLOCK_SIZE) {
			$pad = 0;
		}
		return substr($text, 0, (strlen($text) - $pad));
	}
}
