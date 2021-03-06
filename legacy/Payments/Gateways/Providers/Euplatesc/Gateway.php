<?phpnamespace ByTIC\Common\Payments\Gateways\Providers\Euplatesc;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway as AbstractGateway;

/** * Class Gateway * @package ByTIC\Common\Payments\Gateways\Providers\Euplatesc * */class Gateway extends AbstractGateway
{    /**     * @param $value     * @return mixed     */
    public function setMid($value)
    {
        return $this->setParameter('mid', $value);
    }
    /**         * @param $value         * @return mixed         */
    public function setKey($value)
    {
        return $this->setParameter('key', $value);
    }
    /**         * @return bool         */
    public function isActive()
    {
        if ($this->getMid() && $this->getKey()) {
            return true;
        }
        return false;
    }
    /**         * @return mixed         */
    public function getMid()
    {
        return $this->getParameter('mid');
    }
    /**         * @return mixed         */
    public function getKey()
    {
        return $this->getParameter('key');
    }
}
