<?php

namespace MagentoFilters;

/**
 * Magento APIv2 filter builder
 */
class Builder {

    /**
     * Stores specified conditions
     *
     * @var array
     */
    private $conditions = array();

    /**
     * Whether filter is simple
     *
     * @var bool
     */
    private $simple = true;

    /**
     * Contains valid condition keys
     *
     * @var array
     */
    private $conditionKeys = array(
        'eq', 'neq', 'like', 'nlike', 'in', 'nin', 'is', 'notnull', 'null',
        'gt', 'lt', 'gteq', 'lteq', 'finset', 'regexp', 'from', 'to', 'seq',
        'sneq'
    );

    /**
     * If $array parameter is provided - builds from it if possible
     *
     * @param null $array
     */
    public function __construct($array = null)
    {
        if (null !== $array) {
            $this->fromArray($array);
        }
    }

    /**
     * Adds a condition to the list
     *
     * NOTE: Currently you can't have more than one filter for a field in V2 API
     *
     * @param $condition
     * @param $key
     * @param $value
     * @return $this
     */
    private function addCondition($condition, $key, $value)
    {
        if ($condition !== 'eq') {
            $this->simple = false;
        }
        $this->conditions[$key] = array(
            'condition' => $condition,
            'key' => $key,
            'value' => $value
        );
        return $this;
    }

    /**
     * Builds a simple filter
     *
     * @return array
     */
    private function buildSimpleFilter()
    {
        $filters = array('filter' => array());
        foreach ($this->conditions as $condition) {
            $filters['filter'][] = array(
                'key' => $condition['key'],
                'value' => $condition['value']
            );
        }
        return $filters;
    }

    /**
     * Builds a complex filter
     *
     * @return array
     */
    private function buildComplexFilter()
    {
        $filters = array('complex_filter' => array());
        foreach ($this->conditions as $condition) {
            $filters['complex_filter'][] = array(
                'key' => $condition['key'],
                'value' => array(
                    'key' => $condition['condition'],
                    'value' => $condition['value']
                )
            );
        }
        return $filters;
    }

    /**
     * Return true if filter is simple (has only 'eq' conditions)
     *
     * @return bool
     */
    public function isSimple()
    {
        return $this->simple;
    }

    /**
     * Returns true if filter is not simple (has other than 'eq' conditions)
     *
     * @return bool
     */
    public function isComplex()
    {
        return !$this->simple;
    }

    /**
     * Builds a filter array based on specified conditions
     *
     * @return array
     */
    public function toArray()
    {
        if (empty($this->conditions)) {
            return array();
        }

        if ($this->isSimple()) {
            return $this->buildSimpleFilter();
        }

        return $this->buildComplexFilter();
    }

    /**
     * Validates an array to be valid magento filter
     * This does not check logical validity, only syntactial
     *
     * @param array $array
     * @return bool
     */
    public function validate(array $array)
    {
        if (array_key_exists('filter', $array)) {
            foreach ($array['filter'] as $simple) {
                if (!$this->validateAssociativeEntity($simple)) {
                    return false;
                }

                if (!in_array($simple['key'], $this->conditionKeys)) {
                    return false;
                }
            }
        }

        if (array_key_exists('complex_filter', $array)) {
            foreach ($array['complex_filter'] as $complex) {
                if (!$this->validateAssociativeEntity($complex)) {
                    return false;
                }

                if (!$this->validateAssociativeEntity($complex['value'])) {
                    return false;
                }

                if (!in_array($complex['value']['key'], $this->conditionKeys)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Validates an associative entity, a building block of filters
     *
     * @param $entity
     * @return bool
     */
    private function validateAssociativeEntity($entity) {
        return is_array($entity) &&
                array_key_exists('key', $entity) &&
                array_key_exists('value', $entity);
    }

    /**
     * Equals condition
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function eq($field, $value)
    {
        return $this->addCondition('eq', $field, $value);
    }

    /**
     * Alias of eq (field = ?)
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function equals($field, $value)
    {
        return $this->eq($field, $value);
    }

    /**
     * Not equals condition (field != ?)
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function neq($field, $value)
    {
        return $this->addCondition('neq', $field, $value);
    }

    /**
     * Alias of neq
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function notEquals($field, $value)
    {
        return $this->addCondition('neq', $field, $value);
    }

    /**
     * Like condition (field LIKE ?)
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function like($field, $value)
    {
        return $this->addCondition('like', $field, $value);
    }

    /**
     * Not like condition (field NOT LIKE ?)
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function nlike($field, $value)
    {
        return $this->addCondition('nlike', $field, $value);
    }

    /**
     * nlike alias
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function notLike($field, $value)
    {
        return $this->nlike($field, $value);
    }

    /**
     * In condition (field IN (?))
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function in($field, $value)
    {
        return $this->addCondition('in', $field, $value);
    }

    /**
     * Not in condition (field NOT IN (?))
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function nin($field, $value)
    {
        return $this->addCondition('nin', $field, $value);
    }

    /**
     * nin alias
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function notIn($field, $value)
    {
        return $this->nin($field, $value);
    }

    /**
     * Is condition (field IS ?)
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function is($field, $value)
    {
        return $this->addCondition('is', $field, $value);
    }

    /**
     * Not null condition (field IS NOT NULL)
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function notNull($field, $value)
    {
        return $this->addCondition('notnull', $field, $value);
    }

    /**
     * Is null condition (field IS NULL)
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function isNull($field, $value)
    {
        return $this->addCondition('null', $field, $value);
    }

    /**
     * Greater than condition (field > ?)
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function gt($field, $value)
    {
        return $this->addCondition('gt', $field, $value);
    }

    /**
     * Alias of gt
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function greaterThan($field, $value)
    {
        return $this->gt($field, $value);
    }

    /**
     * Less than condition (field < ?)
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function lt($field, $value)
    {
        return $this->addCondition('lt', $field, $value);
    }

    /**
     * Alias of lt
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function lessThan($field, $value)
    {
        return $this->lt($field, $value);
    }

    /**
     * Greater than or equals condition (field >= ?)
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function gteq($field, $value)
    {
        return $this->addCondition('gteq', $field, $value);
    }

    /**
     * Alias of gteq
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function greaterThanOrEquals($field, $value)
    {
        return $this->gteq($field, $value);
    }

    /**
     * Less than or equals to condition (field <= ?)
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function lteq($field, $value)
    {
        return $this->addCondition('lteq', $field, $value);
    }

    /**
     * Alias of lteq
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function lessThanOrEquals($field, $value)
    {
        return $this->lteq($field, $value);
    }

    /**
     * Find in set condition (FIND_IN_SET(?, field))
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function findInSet($field, $value)
    {
        return $this->addCondition('finset', $field, $value);
    }

    /**
     * Regexp condition (field REGEXP ?)
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function regexp($field, $value)
    {
        return $this->addCondition('regexp', $field, $value);
    }

    /**
     * From condition (same as gteq (field >= ?))
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function from($field, $value)
    {
        return $this->addCondition('from', $field, $value);
    }

    /**
     * To condition (same as lteq (field <= ?))
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function to($field, $value)
    {
        return $this->addCondition('to', $field, $value);
    }

    /**
     * Unused
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function seq($field, $value)
    {
        return $this->addCondition('seq', $field, $value);
    }

    /**
     * Unused
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function sneq($field, $value)
    {
        return $this->addCondition('sneq', $field, $value);
    }
}