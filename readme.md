## Orkiv Inventory

![enter image description here](https://www.orkiv.com/images/inventory.png)

### Requirements 

1. PHP 5.2 and Up
2.  An [Orkiv](https://orkiv.com/i/) Inventory account, with valid API Credentials
3. We recommend using Twitter Bootstrap for building websites with Inventory.php

Download the PHP plugin 'orkiv.inventory.core.php' included in this repo

Initialize the class with the following code. Replace `accountid` with your actual account ID and `apikey` with a valid Key generated for your account.
You can access all of this information under the settings section of your account.

# Initialization
The snippet below initializes the class

    <?php
    
    include "oinventory.core.php";
    
    $manager = new InvManager(array(
      "id" => "accountid",
        "key" => "apikey"
      ));
    
    ?>
    
Once the class is initialized there are three simple calls to retrieve data.
All data is returned in JSON format, and the response is under `$myobject->result`

# Retrieving all categories and Items
### (optimal for async workflows)

### InvMaster::all()
Use the snippet below to get everything from your account, assuming the $manager object is still initialized.

    <?php
    $someobject = $manager->all();
  
# Querying
### Querying with built in pagination
## InvMaster::query(int page,string categoryid, string min_price, string max_price, array additionalquery [ );

When querying you can turn Price min and max constraints off by using the string value `OFF`.

The first page is always 0.  The method below is a  DEFAULT call to the api without any search constraints. Since the category field is  (empty) `""` it will not take the category value of each item into consideration when performing the query.

	$manager->query(0,"","OFF","OFF",array());
 
 The additional query field can be used to query for other fields contained with the item. Please see the example below.

	$query = array('color' => 'yellow');
	$data = $manager->query(0, "","OFF","OFF", $query);
 
#Retrieving all Categories

## InvMaster::categories() 
retrieve all categories by calling the method below.

    <?php
    $someobject = $manager->categories();

#Retrieving Items

### InvMaster::items()
#### To get all items
## InvMaster::item(string $itemid)
To simply get all items you can call on the method below, and the method following is used to GET an individual item.
     
         <?php
        $someobject = $manager->items();
		print_r($someobject);
        $someitem = $manager->item(ITEMID);
        ?>
  
The script above will output the items retrieved. If you're inventory is of vast amount we recommend using querying instead of this method.

	stdClass Object ( [approved] => 1423424607
	 [result] => Array 
	( [_id] => stdClass Object ( [$id] => 54d28038c9fe6b807f6bc275 ) [name] => Kicker 250.1   [media] => Array ( ) 
	[buy] => https://orkiv.com/i/buy/?54027800c9fe6b653a7b23c6=54d28038c9fe6b807f6bc275 ) ) )
 
If you noticed the media key of the only item retrieved is empty, this is because no images were uploaded for the item. Here is the data again after giving the item an image via the inventory online portal.

		stdClass Object ( [approved] => 1423424880 
		Array ( [0] =>
		stdClass Object ( [_id] => 
		stdClass Object ( [$id] => 54d28038c9fe6b807f6bc275 ) [name] => Kicker 250.1 [media] => Array ( [0] => https://orkiv.com/i/render.php?54027800c9fe6b653a7b23c6=54d7bd67c9fe6bb4206bc276 ) [buy] => https://orkiv.com/i/buy/?54027800c9fe6b653a7b23c6=54d28038c9fe6b807f6bc275 ) ) )

Now that an image is set you can directly use that as a public link to your items media. 
 
### Breakdown of the Orkiv Inventory Item
In each item object, there is a buy and media property.
Media is an array of https links to that item's images.
And Buy is the link to the payment Gateway for that item.

## Accessing cart checkout

		https://orkiv.com/i/cart-checkout/?YOURACCOUNTID=SAMPLEITEMID,SAMPLEITEMID2&SAMPLEITEMID=quantity

Cart checkout is accessed via link. You may add as many desired items to include in checkout.

### Opening an Order
## InvMaster::order(string $order_id)

	$request = $manager->order($order_id);
	$order = $request->result;
	
If the Order exists it will return a json object as the Result if the Order was not found, the result will be `404` 

### Creating a new Item
## InvMaster::add(string name,array $properties [);
Creating items via api can be useful for importing previous inventory items by adding them programmatically.

     //add function is used to create a new item
     $myitemname = "Banana";
     $manager->add($myitemname, array("price" => "10000","customfield" => "somevalue"));

### Updating an Item
## InvMaster::update(string itemid, array $set [ )
Updating an item's properties can be useful for updating item data to match actual inventory data. The code below will update an item's price and quantity. Any non-existing key supplied will be added automatically.

	//valid item id needed
	$ITEMID = "AAAAA";
	$manager->update($ITEMID,array("price" => 9000, "quantity" => "20"));

### Removing an item
##  InvMaster::delete(string itemid)
	//ITEMID still being "AAAAA"
	$manager->delete($ITEMID);

### Writing web hooks
##  InvMaster::write(string callbackurl, string serviceid, array $_POST [ )

An event is a post request that Inventory performs after a successful transaction. This is useful to create any additional login accounts for the services that you are selling.
		
	// Setup Post Data
	$postData = array("email" => "foo", "check" => "bar");
	//Creating an webhook
	$manager->write("url", "serviceID", $postData  );

#### Using Nano Identifiers

Nano Identifiers are dynamic post variables that can be used in URL callbacks. It allows the personalization of each callback request. You can only retrieve order data. In the example below we will create a web hook and use a nano identifier pointing to the customer's email
		
	//sample Nano Identifier
	$nano = "@:info_email";

	// Setup Post Data
	$postData = array("email" => $nano, "check" => "@:order_id");
	//Creating an webhook
	$manager->write("url", "serviceID", $postData  );

##Additional code responses

To get an idea of the fields available in an order look at this sample Order Row data.

			stdClass Object
		(
    [approved] => 1418161210
    [result] => stdClass Object
        (
            [_id] => stdClass Object
                (
                    [$id] => 548711bec9fe6b2e71f80567
                )

            [info_email] => acme@acme.com
            [info_first] => Jack
            [info_last] => Smith
            [info_adr1] => Acme
            [info_adr2] => 
            [info_cty] => Acme
            [info_zip] => 0000
            [state] => AL
            [info_sadr1] => 
            [info_sadr2] => 
            [info_scty] => 
            [info_szip] => 
            [sstate] => AL
            [shipset] => true
            [order_id] => 7A6823BDB59806E0971EC5DA88C83529
            [itemIDs] => Array
                (
                    [0] => 5486144fc9fe6b4f3bf80566
                    [1] => 
                )

            [proc] => 1
            [amount_total] => 49900
        )

	)



            
2014 Orkiv LLC    
