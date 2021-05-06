<?php

class MapNavigator {

    //All the maps we have and thier ID's. This can probably by populated with WP_Query but it works right now
    public $mapsNames = array(
        '1518' => 'North America',
        '1555' => 'South America',
        '1839' => 'Asia',
        '1894' => 'United States',
        '1895' => 'Mexico',
        '1896' => 'Argentina',
        '1897' => 'Russia',
        '1905' => 'United Kingdom',
        '1904' => 'Italy',
        '1903' => 'France',
        '1902' => 'Europe',
        '1950' => 'Africa',
        '1951' => 'Cameroon',
        '1952' => 'Jordan',
        '1953' => 'Pakistan',
        '1954' => 'Romania',
        '1955' => 'Switzerland',
        '1976' => 'Vietnam',
        '2033' => 'India',
        '2038' => 'Serbia',
        '2070' => 'Colombia',
        '2085' => 'South-Africa',
        '2120' => 'Slovenia',
        '2130' => 'Netherlands',
        '2154' => 'Spain',
        '2153' => 'Portugal',
        '2176' => 'China',
	'2189' => 'Ecuador',
	'2228' => 'Thailand',
	'2305' => 'Brazil'

    );

    //This describes how the maps and cities are related. Required for navigating
    public $mapsTree = array(
        '1518' =>  array( //North America
            '1894' =>  array( //United States
                'Washington DC',
                'Silicon Valley',
                'San Diego',
                'New York',
                'Los Angeles',
                'Chicago',
                'Seattle',
                'Portland',
                'Atlanta',
		'Sacramento',
		'Phoenix',
		'Denver',
		'Austin'
            ),
            '1895' =>  array(//Mexico
                'Mexico City',
            ),
        ),
        '1555' => array( //South america
            '1896' => array( //Argentina
                'Buenos Aires',
	    ),
            '2070' => array( //Colombia
                'Bogotá',
            ),
            '2189' => array(//Ecuador
                'Quito',
	    ),
	    '2305' => array(//Brazil
		'Porto Alegre',
	    ),

        ),
        '1839' => array( //Asia
            '1897' => array( //Russia
                'Moscow',
            ),
            '1952' => array( //Jordan
                'Amman',
            ),
            '1953' => array( //Pakistan
                'Islamabad',
            ),
            '1976' => array(//Vietnam
                'Ho Chi Minh'
            ),
       	    '2033' => array(//India
                'Mumbai'
	     ),
            '2176' => array(//China
		'Shanghai'
	    ),
	    '2228' => array(//Thailand
		'Bangkok'
	    )

    	),
        '1902' =>  array( //Europe
            '1903' =>  array( //France
                'Paris',
            ),
            '1904' =>  array(//Italy
		'Milan',
                'Rome',
	        ),
       	    '1905' =>  array(//United kingdom
                'London',
            ),
            '1954' =>  array(//Romania
                'Bucharest',
                ),
            '1955' =>  array(//Switzerland
                'Geneva',
            ),
            '2038' =>  array(//Serbia
                'Belgrade',
            ),
            '2120' =>  array(//Slovenia
                'Ljubljana',
            ),
            '2130' =>  array(//Netherlands
		        'Maastricht',
                'Amsterdam',
            ),
            '2154' =>  array(//Spain
		 'Madrid',
		 'Barcelona',
            ),
            '2153' =>  array(//Netherlands
                'Lisbon',
            ),
        ),
	'1950' =>  array( //Africa
            '1951' =>  array( //Cameroon
                'Douala'
	        ),
	    '2085' =>  array( //South Africa
                'Johannesburg'
            )
	)
    );

    function __construct()
    {
        //$this->sortMaps();
    }

    public function sortArrayByArray(array $array, array $orderArray)
    {
        $ordered = array();

        foreach ($orderArray as $key => $value) {
            if (array_key_exists($key, $array)) {
                $ordered[$key] = $array[$key];
                unset($array[$key]);

            }

        }

        return $ordered + $array;
    }

    public function sortMaps()
    {
        asort($this->mapsNames);
        $this->mapsTree = $this->sortArrayByArray($this->mapsTree, $this->mapsNames);
        foreach ($this->mapsTree as $key => $value) //looping through first layer
        {
            $this->mapsTree[$key] = $this->sortArrayByArray($value, $this->mapsNames);
            foreach ($value as $key2 => $value2) {
                asort($value2);//sorts arrays of cities
                $this->mapsTree[$key][$key2] = $value2;
            }
        }
    }

    //Takes a map identifier string: id or name,
    //Returns an array with map['id'] and map['name']
    //Used when either id or name is not available.
    public function getMapIdAndName($map)
    {
        foreach ($this->mapsNames as $key => $value) //looping through first layer
        {
            if($map == $key || $map == $value)
            {
                return array( 'id' => $key, 'name' => $value);
            }
        }
        return null;
    }

    //Returns all the maps in the first layer of the tree.
    // Key is the map id maps['1518'] = 'North America'
    public function getAllFirstLayerMaps()
    {
        $firstLayerMapsArray = array();
        foreach ($this->mapsTree as $key => $value) //looping through first layer
        {

            $firstLayerMapsArray = $firstLayerMapsArray + array($this->getMapIdAndName($key)['id'] => $this->getMapIdAndName($key)['name']);
        }

        return $firstLayerMapsArray;
    }

    //Returns all the maps in the second layer of the tree.
    //Key is the map id maps['1892'] = 'United States'
    public function getAllSecondLayerMaps()
    {
        $secondLayerMapsArray = array();
        foreach ($this->mapsTree as $key => $value) //looping through first layer
        {

            foreach ($value as $key2 => $value2)
            {
                $secondLayerMapsArray = $secondLayerMapsArray + array($this->getMapIdAndName($key2)['id'] => $this->getMapIdAndName($key2)['name']);
            }
        }

        return  $secondLayerMapsArray;
    }

    //Takes a map identifier string: id or name
    //Returns the id of the parent of a second layer map
    public function findParentMap($map)
    {

        $id = $this->getMapIdAndName($map)['id'];

        foreach ($this->mapsTree as $id1 => $children1) //looping through first layer
        {
            foreach ($children1 as $id2 => $children2) //looping through second layer looking for a match
            {
                if($id == $id2)
                {
                    return $id1; //return first layer id
                }
            }
        }

        return null;
    }

    //Takes a map identifier string: id or name
    //Returns an array of child maps
    //Key is the map id maps['1892'] = 'United States'
    public function getChildMaps($map)
    {

        $id = $this->getMapIdAndName($map)['id'];
        $childMaps = array();
//        var_dump($id);
        foreach ($this->mapsTree[$id] as $childId => $children) //looping through first layer
        {
            $childMaps = $childMaps + array($this->getMapIdAndName($childId)['id'] => $this->getMapIdAndName($childId)['name']);
        }
        return $childMaps;
    }

    //Takes a map identifier string: id or name
    //Returns an array of all regions/cities
    //Key is a simple index maps[0] = 'Washington DC'
    public function getRegions($map)
    {

        $id = $this->getMapIdAndName($map)['id'];
        foreach ($this->mapsTree as $id1 => $children1) //looping through first layer
        {
            if($id  == $id1) //if the map is in the first layer
            {
                $allRegionsFromChildren = array();
                foreach ($children1 as $id2 => $children2) //looping through second layer and accumulating an array of all regions
                {
                    $allRegionsFromChildren = array_merge($allRegionsFromChildren, $children2);
                }
                return $allRegionsFromChildren; //return array of regions from all children bellow
            }
            else
            {
                foreach ($children1 as $id2 => $children2) //looping through second layer looking for a match
                {
                    if($id  == $id2)
                    {
                        return $children2; //return array of regions
                    }
                }
            }
        }
    }
}


//Tests

//$mapNavigator = new MapNavigator();
//var_dump($mapNavigator);

////echo "<br>Get actual parent by name:";
//var_dump($mapNavigator->findParentMap('United States'));

//echo "<br>Get all first layer maps:";
//var_dump($mapNavigator->getAllFirstLayerMaps());

//echo "<br>Get all second layer maps:";
//var_dump($mapNavigator->getAllSecondLayerMaps());

//echo "<br>Get child maps";
//var_dump($mapNavigator->getChildMaps('North America'));

//echo "<br>Get actual parent by id:";
//var_dump($mapNavigator->findParentMap('1892'));
//
//echo "<br>Get null parent by id:";
//var_dump($mapNavigator->findParentMap('1518'));
//
//echo "<br>Get name by id ";
//var_dump($mapNavigator->getMapIdAndName('1518')) ;
//
//echo "<br>Get all regions top level";
//var_dump($mapNavigator->getRegions('1518'));
//
//echo "<br>Get all regions 2nd level";
//var_dump($mapNavigator->getRegions('Mexico'));
//
//echo "<br>Get all regions 2nd level #2";
//var_dump($mapNavigator->getRegions('United States'));

//echo json_encode($mapsTree);

