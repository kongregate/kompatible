# kompatible

An api for hosting games on both Kongregate and Facebook

## Handling kongregate and facebook credentials.

You'll need your application credentials from both Facebook and Kongregate. You can get your Kongregate game credentials at /games/< username >/< game >/api. Then, depending on if the request is from Kongregate or Facebook, we will initialize our $kompatible object differently. 

    if (isset($_REQUEST['platform']) && $_REQUEST['platform'] == "fb") {
      $kompatible = new FacebookPlatform($config['facebook']);	
    } else {
      $kompatible = new KongregatePlatform($config['kongregate']);
    }
    
I've added a configuration file, config.json.example, for storing both credentials. 

In your Facebook configuration, you'll want to add ?platform=fb to the end of your canvas address.

## User login

Then we'll want to make sure the user is logged in. If they aren't, they will be redirected to the site specific login form.

    $kompatible->login();
    
Once the user is logged in, we can access data through the $kompatible

    $kompatible->getUserName();
    $kompatible->getFriends(); //returns empty array for Kongregate

## Load the Site Specific API

If your game needs to use the site specific javacript api (to submit stats, make purchase requests, etc) then we'll need to load those libraries:

    $kompatible->loadLibraries();

## Serving your game file

For flash games, you should customize the $kompatible->getFlashParams() function. These are the parameters to pass along to the flash game itself.

    public function getFlashParams() {
    	$params = "&user_id={$this->user}";
    	return $params;
    }

Then you can display the flash file in the page with:

    $kompatible->displayFlashFile();
    
## Getting friends

For both apis, there is a function for getting the friends list:

    $kompatible->getFriends();
    
which returns a similar array for both cases: ("data" => array(array("name" => "username", "id" => "username")...))

## Kongregate Microtransactions 

KongregatePlatform.php is setup to check the api on first request to see if the user has purchased any items. 

If they do, then we automatically mark all the items as used.

First, we get a list of all our available items from the server. (/games/< username >/< game >/items)

    $kompatible->getGameItems();

We can get a users specific inventory:

    $data = $kompatible->getKredsInventory();
    $data['items']
    
If we want to mark all the items as used:

    $kompatible->updateInventory();

Or if we want to use a single item on page load:
    
    $kompatible->useKredItem($itemData);

The Kongregate microtransaction docs are here: [microtransactions api](http://www.kongregate.com/developer_center/docs/microtransaction-client-api "Transaction API Docs").

## Examples

index.php is the basic example with login and microtransactions

## TODO

* Add a generic version of the FB request dialog calls.
* Comments
* Better demo of platform features
* Styling
* Add tests

## Note on Patches/Pull Requests

* Fork the project.
* Make your feature addition or bug fix.
* Add tests if you want
* Commit
* Send me a pull request. Bonus points for topic branches.

## Copyright

Copyright (c) 2011 metal games, kongregate. See LICENSE for details.

