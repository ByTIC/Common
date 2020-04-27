<?php

namespace ByTIC\Common\Security\ACL\Permissions;

class Permissions extends Records
{

	/**
	 * Singleton
	 *
	 * @return ACL_Permissions
	 */
	public static function instance()
	{
		static $instance;
		if (!($instance instanceof self)) {
			$instance = new self();
		}
		return $instance;
	}

}