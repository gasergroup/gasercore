<?php namespace App\Libraries;

/**
 * Custom library to generate item codes, according to a type
 * 
 * @author Ricardo Yubal <support@sglancer.com>
 */
class CodeGenerator {
	/**
	 * To generate a code
	 * 
	 * Types available:
	 * none - Non-barcode code, just random numbers
	 * code39 - Maximum 43 chars (A-Z0-9 -.$/+%)
	 * code128 - Maximum 128 ASCII characters
	 * ean-8 - 8-digit code, where the 8th is a check digit
	 * ean-13 - 13-digit code, where the 13th is a check digit
	 * upc-a - 12-digit code, where the 12th is a check digit
	 * qr - Maximum 4296 ASCII characters
	 * 
	 * @param string $type - The code type
	 * @return string|null Code
	 */
	public function generateCode($type) {
		if($type == 'none')
			return $this->digitsCode('none');
		else if($type == 'code39' || $type == 'code128')
			return $this->charactersCode($type);
		else if($type == 'ean-8' || $type == 'ean-13' || $type == 'upc-a')
			return $this->digitsCode($type);
		else if($type == 'qr')
			return $this->alphanumericCode(20);
		
		return null;
	}

	// Type can be code39 or code128
	private function charactersCode($type) {
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 -.$/+%";

		// Code39 doesn't accept lowercase
		if($type == 'code39')
			$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 -.$/+%";

		$length = ($type == 'code39') ? 8 : 13;

		$code = '';
		for($i = 0; $i < $length; $i++) {
			$charIndex = rand(0, strlen($chars) - 1);
			$newChar = $chars[$charIndex];

			// Make sure first or last character isn't a space
			if($newChar == ' ' && ($i == 0 || $i == $length - 1))
				$newChar = $chars[0];

			$code .= $newChar;
		}

		return $code;
	}

	// Type can be none, ean-8, ean-13 or upc-a
	private function digitsCode($type) {
		$chars = '0123456789';

		$lengths = [
			'none' => 12,
			'ean-8' => 7,
			'ean-13' => 12,
			'upc-a' => 11
		];

		$code = '';
		$checksum = 0;
		for($i = 0; $i < $lengths[$type]; $i++) {
			$charIndex = rand(0, strlen($chars) - 1);
			$newDigit = $chars[$charIndex];

			$code .= $newDigit;

			if($type == 'ean-8' || $type == 'upc-a')
				$checksum += ($i%2 == 0) ? ($newDigit*3) : $newDigit;
			else
				$checksum += ($i%2 == 0) ? $newDigit : ($newDigit*3);
		}

		// Calculate check digit
		$check_digit = (ceil($checksum / 10) * 10) - $checksum;

		return $code . $check_digit;
	}

	private function alphanumericCode($length) {
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

		$code = '';
		for($i = 0; $i < $length; $i++) {
			$charIndex = rand(0, strlen($chars) - 1);
			$newChar = $chars[$charIndex];
			$code .= $newChar;
		}

		return $code;
	}
}