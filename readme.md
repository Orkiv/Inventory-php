## Orkiv Inventory

![desc](https://cdn2.iconfinder.com/data/icons/windows-8-metro-style/128/php.png) ![enter image description here](https://www.orkiv.com/images/inventory.png)

### Requirements 

1. PHP 5.2 and Up
2.  An [Orkiv](https://orkiv.com/i/) Inventory account, with valid API Credentials
3. We recommend using Twitter Bootstrap for building websites with Inventory.php

Download the PHP file 'inventory.core.php' included in this repo

Initialize the class with the following code. Replace `accountid` with your actual account ID and `apikey` with a valid Key generated for your account.
You can access all of this information under the settings section of your account.

# Navigation

 1. [Setup](#setup)
 2. [Initialization](#initialization)
 3. [Getting Inventory data](#get-all-inventory-data)
 4. [Inventory Group object](#inventory-group-object)
 4. [Performing a query](#querying)
 5. [Get all categories](#get-all-categories)
 6. [Category Object](#category-object)
 7. [Get inventory items](#get-inventory-items)
 8. [Item object](#item-object)
 9. [Accessing cart checkout](#accessing-cart-checkout)
 10. [Orders](#orders)
 11. [Order object](#order-object) 
 12. [onSubscription event](#subscription-callbacks)

# Setup

Move the php file `inventory.php` to your website's root. `include` the file wherever functionality is desired!

# Initialization
The snippet below initializes the class

    <?php
    
    include "inventory.core.php";
    
    $manager = new InvManager(array(
      "id" => "accountid",
        "key" => "apikey"
      ));
    
    ?>
    


# Get all inventory data.
### (caching inventory data)

###  InvMaster::all() 
Used to download inventory data.

#### Returns

This will return a JSON object with key `result`, which is an array of [inventory group objects](#inventory-group-object).

    <?php
    $someobject = $manager->all()->result;

## Inventory Group object

### Properties

`name` : Name of the current category.
`items` : Array of [Item objects](#item-object).
    

  
# Querying
### Querying with built in pagination
## InvMaster::query(int page,string categoryid, string min_price, string max_price, mixed $additionalquery [ );

When querying you can turn Price min and max constraints off by using the string value `OFF`.

The first page is always 0.  The method below is a  DEFAULT call to the api without any search constraints. Since the category field is  (empty) `""` it will not take the category value of each item into consideration when performing the query.

    $manager->query(0,"","OFF","OFF",array());
 
 The additional query field can be used to query for other fields contained with the item. Please see the example below.

    $query = array('color' => 'yellow');
    $data = $manager->query(0, "","OFF","OFF", $query)->result;
 
# Get all categories

## InvMaster::categories() 
retrieve all categories by calling the method below.

### Returns

This method returns a  mixed array with key `result`, this key is an array of [category objects](#category-object)

    <?php
    $someobject = $manager->categories()->result;

## Category object

 ### Properties

`name` : Name of category.
`id`: ID of category.
`children`: Array of category objects, with the exception being that `sampleimage` is used as a system generated image for that category.
`tmp` :   System generated image for that category.


#Get inventory Items

### InvMaster::items()
#### To get all items
## InvMaster::item(string $itemid)
To simply get all items you can call on the method below, and the method following is used to GET an individual item.
     
         <?php
        $someobject = $manager->items()->result;
        print_r($someobject);
        $someitem = $manager->item(ITEMID)->result;
        ?>
  
The script above will output the items retrieved. If you're inventory is of vast amount we recommend using querying instead of this method.

Expected response : 

    stdClass Object ( [approved] => 1423424607
     [result] => Array 
    ( [_id] => stdClass Object ( [$id] => 54d28038c9fe6b807f6bc275 ) [name] => Kicker 250.1   [media] => Array ( ) 
    [buy] => https://orkiv.com/i/buy/?54027800c9fe6b653a7b23c6=54d28038c9fe6b807f6bc275 ) ) )
 
- If you noticed the media key of the only item retrieved is empty, this is because no images were uploaded for the item. Here is the data again after giving the item an image via the inventory online portal.

        stdClass Object ( [approved] => 1423424880 
        Array ( [0] =>
        stdClass Object ( [_id] => 
        stdClass Object ( [$id] => 54d28038c9fe6b807f6bc275 ) [name] => Kicker 250.1 [media] => Array ( [0] => https://orkiv.com/i/render.php?54027800c9fe6b653a7b23c6=54d7bd67c9fe6bb4206bc276 ) [buy] => https://orkiv.com/i/buy/?54027800c9fe6b653a7b23c6=54d28038c9fe6b807f6bc275 ) ) )

Now that an image is set you can directly use that as a public link to your items media. 
 
##  Item Object
In each item object, there is a buy and media property.
Media is an array of https links to that item's images.
And Buy is the link to the payment Gateway for that item.

### Properties 

`category` : Category ID of item.
`desc` : Richtext description of item.
`name` : Name of item.
`ordprice` : Integer value of price.
`price`: String value of price.
`quantity` : Item inventory quantity.
`media` : Array of strings. Each string is a url to an item's image.
`Buy` : Direct buy link of item.



### Creating a new Item
## InvMaster::add(string name,mixed $properties [);
Creating items via api can be useful for importing previous inventory items by adding them programmatically.

     //add function is used to create a new item
     $myitemname = "Banana";
     $manager->add($myitemname, array("price" => "10000","customfield" => "somevalue"));

### Updating an Item
## InvMaster::update(string itemid, mixed $set [ )
Updating an item's properties can be useful for updating item data to match actual inventory data. The code below will update an item's price and quantity. Any non-existing key supplied will be added automatically.

    //valid item id needed
    $ITEMID = "AAAAA";
    $manager->update($ITEMID,array("price" => 9000, "quantity" => "20"));

### Removing an item
##  InvMaster::delete(string itemid)
    //ITEMID still being "AAAAA"
    $manager->delete($ITEMID);


# Accessing cart checkout

        https://orkiv.com/i/cart-checkout/?YOURACCOUNTID=SAMPLEITEMID,SAMPLEITEMID2&SAMPLEITEMID=quantity

Cart checkout is accessed via link. You may add as many desired items to include in checkout.

# Orders
### Opening an Order
## InvMaster::order(string $order_id)

    $request = $manager->order($order_id);
    $order = $request->result;
    
If the Order exists it will return a json object as the Result if the Order was not found, the result will be `404` 

### Fetching all orders
## InvMaster::orders()

This will retrieve all the orders within an account.

    $request = $manager->orders();
    $orders = $request->result;

## Order object

### Properties

`info_email` : Customer email
`info_first` : Customer first name
`info_last` : Customer last name
`phone` :  Customer phone number (U.S. format)
`shipset`: Customer billing address matches shipping address
`info_adr1` : Customer billing address line 1
`info_adr2` : Customer billing address line 2
`info_cty` : Customer billing city 
`info_zip` : Customer billing zip
`state` : Customer billing state
`info_sadr1` : Customer shipping address line 1
`info_sadr2` : Customer shipping address line 2
`info_scty` : Customer shipping city 
`info_szip` : Customer shipping zip
`sstate` : Customer shipping state
`tax_amount`: Float value of tax amount for this order in hundreds. Divide by 100 for USD value
`shipping_amount`: Float value of shipping total in USD. No conversion needed
`amount_total` : Float value of total order in USD.
`order_id` : Unique order ID

    

### Querying orders
## InvMaster::orders(mixed $value [)

### Returns 
Returns an array of [order objects](#order-object) matching the supplied criteria.

Example :

    <?php
    $order = $manager->orders(array("order_id" => "AAA"))->result;
     ?>

# Subscription callbacks

An event is a post request that Inventory performs after a successful transaction. This is useful to create any additional login accounts for the services that you are selling.

##  InvMaster::write(string callbackurl, string serviceid, mixed $value [ )
    
    This function will create an event. 
        
    Example :
    // Setup Post Data
    $postData = array("email" => "foo", "check" => "bar");
    //Creating an webhook
    $manager->write("url", "serviceID", $postData  );

#### Using Nano Identifiers

Nano Identifiers are dynamic post variables that can be used in URL callbacks. It allows the personalization of each callback request. You can only assign order data to nano identifiers. In the example below we will create a web hook and use a nano identifier pointing to the customer's email
        
    //sample Nano Identifier
    $nano = "@:info_email";

    // Setup Post Data
    $postData = array("email" => $nano, "check" => "@:order_id");
    //Creating an webhook
    $manager->write("url", "serviceID", $postData  );



            
2016 Orkiv Retail Solutions LLC    
