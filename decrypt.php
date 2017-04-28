<?php
/**
 * RPG Maker MV file decryption helper
 */
class Decrypter
{
	const HEADER_LEN = 16;
	const SIGNATURE = "5250474d56000000";

	protected $key;

	/**
	 * Initialize decrypter with a hex key
	 * @param string $key Hexadecimal-form encryption key from System.json
	 */
	public function __construct(string $key)
	{
		$this->key = hex2bin($key);
	}

	/**
	 * Decrypt raw file data with an encryption key
	 * @param  string $data
	 * @param  string $key
	 * @return string
	 * @throws Exception
	 */
	public function decrypt(string $data)
	{
		if (strlen($data) < self::HEADER_LEN * 2) {
			throw new Exception("File is to short (<" . (self::HEADER_LEN * 2) . " bytes)");
		}
		if (strcmp(substr($data, 0, self::HEADER_LEN / 2), hex2bin(self::SIGNATURE)) !== 0) {
			throw new Exception("File header does not match expected signature.");
		}

		// Trim fake header and decrypt the original header
		// The actual file contents after the first 16 bytes is not encrypted
		$data = substr($data, self::HEADER_LEN);
		$out = '';
		for($i = 0; $i < self::HEADER_LEN; $i++) {
			$out .= substr($data, $i, 1) ^ substr($this->key, $i, 1);
		}
		$out .= substr($data, self::HEADER_LEN);

		return $out;
	}
}
