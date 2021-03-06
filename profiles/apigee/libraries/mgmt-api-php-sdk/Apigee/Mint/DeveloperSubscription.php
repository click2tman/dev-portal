<?php
namespace Apigee\Mint;

// TODO: finish this class when the docs are finished

class DeveloperSubscription extends Base\BaseObject
{

    private $developerId;
    private $productId;
    private $term;

    /**
     * Organization
     * @var \Apigee\Mint\Organization
     */
    private $organization;

    /**
     * @var \Apigee\Mint\Product
     */
    private $products = array();

    /**
     * com.apigee.mint.model.Developer
     * @var \Apigee\Mint\Developer
     */
    private $developer;

    private $id;

    public function __construct($developer_id, $product_id, $term, \Apigee\Util\OrgConfig $config)
    {
        $base_url = '/mint/organizations/' . rawurlencode($config->orgName) . '/developers/' . rawurlencode($developer_id) . '/products/' . rawurlencode($product_id) . '/TERM/' . rawurlencode($term) . '/subscriptions';
        $this->init($config, $base_url);

        $this->developerId = $developer_id;
        $this->productId = $product_id;
        $this->term = $term;

        $this->wrapperTag = 'developerSubscriptionDetail';
        // TODO: verify the following two items when docs are fleshed out
        $this->idField = 'id';
        $this->idIsAutogenerated = true;

        $this->initValues();
    }

    protected function initValues()
    {
        // TODO
        $this->id = null;
    }

    public function instantiateNew()
    {
        return new DeveloperSubscription($this->developerId, $this->productId, $this->term, $this->config);
    }

    public function loadFromRawData($data, $reset = false)
    {
        if ($reset) {
            $this->initValues();
        }
        $excluded_properties = array('product', 'developer', 'organization');
        foreach (array_keys($data) as $property) {
            if (in_array($property, $excluded_properties)) {
                continue;
            }

            // form the setter method name to invoke setXxxx
            $setter_method = 'set' . ucfirst($property);

            if (method_exists($this, $setter_method)) {
                $this->$setter_method($data[$property]);
            } else {
                self::$logger->notice('No setter method was found for property "' . $property . '"');
            }
        }

        if (isset($data['organization'])) {
            $organization = new Organization($this->config);
            $organization->loadFromRawData($data['organization']);
            $this->organization = $organization;
        }

        if (isset($data['product'])) {
            $product = new Product($this->config);
            $product->loadFromRawData($data['product']);
            $this->products[] = $product;
        }

        if (isset($data['developer'])) {
            $developer = new Developer($this->config);
            $developer->loadFromRawData($data['developer']);
            $this->developer = $developer;
        }
    }

    public function __toString()
    {
        $obj = array();
        $properties = array_keys(get_object_vars($this));
        $excluded_properties = array_keys(get_class_vars(get_parent_class($this)));
        $excluded_properties += array(
            'developerId',
            'productId',
            'term'
        );
        foreach ($properties as $property) {
            if (in_array($property, $excluded_properties)) {
                continue;
            }
            if (isset($this->$property)) {
                $obj[$property] = $this->$property;
            }
        }
        return json_encode($obj);
    }

    public function getId()
    {
        return $this->id;
    }

    // Used in data load invoked by $this->loadFromRawData()
    private function setId($id)
    {
        $this->id = $id;
    }
}
