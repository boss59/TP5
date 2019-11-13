<?php
return array (	
		//应用ID,您的APPID。
		'app_id' => "2016101100657104",
		// 商户id
		'seller_id' => '2088102179010834',

		//商户私钥
		'merchant_private_key' => "MIIEowIBAAKCAQEA1XpSqwdk/CtX+BqjW+cgvqR7iGvFhhl0Z1R7mutFLFdCm8ReKKFt+ozWOoPVwMQo6xz28/OUDBk5ECTxnm+/wkg17sDMhOTOx3eRYn0nP9Qe2wzLJvhcCYApMbQg8TNNsTPznDUP75fkkOk9zDyWW9aGQQkj+2cWXIKzArk4R76Meqz9Ke+k4jKrTNyjX8IINRVg0tUJNhywE2BhBfTiFK+V5apXzK4QNmxOYiFG3z4uEMJfY4XyANJj6uyLqWV3QMO8tFRdgDn0XdX0uy/G4k83s32WP1clyWX2+62Yx9JJ2BXQ8nMLIkDf2QDl8IUo+EpKRjQa3z19E0WnDIeM0wIDAQABAoIBAD9aOthIrH5teoDFnv8yGzSYaZS1rvu56CKWHsx9z5x2FO1XHD5gkdvOGfFpmHDJhf8oLF1/RstEXPveitni/fxxI5hEHS2/HWJyqHGbBJ/bmoCVTmC1SdMD9rWqaDBMcl0JNt1/ncgTwqACp/VJcbG1e0gnp0b/c9Jmcyt2vi4D4p/EhC0Pg+cyhHhp/b4Nn2F/cXoq/Ugx4hD74iScj8guSQVJGZ62azu6WM8+HKIm2D+r/DmBaDL2PFi98WgZM9ZYU/xMom3TzF18h+QOFaxPC2tdw9sf72Oje4Ty4ZkLe/hT63Mpwsd0vOQPKD6YvrAKfiQgjzHDy+VirzOFMekCgYEA8uZqDL0u2Urqq+mlzk06DKuy3oek7pYHFi3MbJD66AAx4+iBs25V+P+3ahRrGJSNdJvpf7csXjaJ+IoAuu9zcBjXtZOGjsU/VfPsUtAUTqNsI1gAhw7NXafOCktX9oc/Y8WHTfkh5T4vV2adZvL8Nz6nmiAKfIFaFNUn5LSbju0CgYEA4P2xOsXeQPPmsM67z/OYHT6cNtnCZpWeL7PS6Pxbps2wNfaMreaXCPujZD/ucIXMIw2s5hO1LhCDUXLRim/2or6DckbgEVPEjq49v/lgPCByEZnZwsTRZ/G/vUwze5TXA+AKOuhLgINDgcarGMXKlSD8cRDKV+BouQhQHJVwUr8CgYB/H13jOuOY719wB4EyPjKiTP34/q6v+y7MSb3SF/JolymFKLswqA8wmMxqJTODksORgsOqhrgeupZsz/Tf4M/em/HQ4tuqPssN9fH5yVLHcTn4KWdlR7btTBRGSPZJXLEtS0Sr5zX5HxlxuY+pQIQFebIHtLsoPB5kBsaxKGC9ZQKBgCzdylLrFGglezJ093T/oazrSGGTyTWFzpa6qGt3r5sy11nbgbBIFIRhEfMy4Iyl0dCHsJvtACtgdZ9vYyPVMfzAOxw10AcEimTdkEjRJQS4f6bM5GT3tL6isdbA9F9Hjh0it0eJ/UxyYKrngEUfZEea3Vx6ua8OfDOx2a/piQ2nAoGBAOIPu8fIZ5dCDizrbEBfVFO0Y0nYbUNqvW+eW4hQ9BqEnArNlRvZ1iqjfUsuxwmw+SXkmCH6LNF6QYDyIy/nUdZnclTCiwGSI4wxk5M6kXiPhB2ZFy/3XSBzDdBe6PyDCCsB3iojR4//DdsnOrPFwX9cbDisC9HkjGAGb6RAB1jS",
		
		//异步通知地址
		'notify_url' => "http://w3.xxoo.cn/ecindex/order/notifypay",
		
		//同步跳转
		'return_url' => "http://w3.xxoo.cn/ecindex/order/returnpay",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmumhpj/E618NIyeWrDqMbKZsB2c7EQk0SRq4aZMajemxcI86oxkw/UZ4QSfvjU0rCkwM3LEqUSjDIpEY9v3NgW+BDVBOihovN3m7lGl6NDdxD+FOg225K5wG029SbwwY9semCHO6Krehs27ZyrlC3mEA/8sexW/L+GFQ2dSlmr1e3pGPQ+NSjUZhM0uZGRz0l4DRxX517iYzymhH+MBehLeTzEz8KYrFemDuSy5qtTrWwk2FmiBzJc6CubXjcJK19h1nKVaiBOYp5jKboTzrqEeUVRwqSY7erVhy4X+HHUfn5BxV2VO7GmGrUiWPIQev2uRvMZpJ58bve2C3xgpA3wIDAQAB",
);