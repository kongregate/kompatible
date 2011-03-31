# synapse

This api is setup for putting a Facebook game on Kongregate as an iframe game, or vice versa.

## Handling kongregate and facebook credentials.

You can get your Kongregate game credentials at /games/< username >/< game >/api. Then, depending on if the request is from Kongregate or Facebook, we will initialize our $platform differently. 

    if (isset($_REQUEST['platform']) && $_REQUEST['platform'] == "fb") {
      ...
      // put facebook credentials in $config
      $platform = new FacebookPlatform($config);	
    } else {
      ...
      // put kongregate credentials in $config
      $platform = new KongregatePlatform($config);
    }
    
Look at the top of index.php for an example.

## Serving your game file

For flash games, you should customize the $platform->getFlashParams() function. These are the parameters to pass along to the flash game itself.

    public function getFlashParams() {
    	$params = "&user_id={$this->user}";
    	return $params;
    }

Then you can display the flash file in the page with:
    
    $platform->displayFlashFile();

## User login

The call to login a user is easy. If they aren't currently logged in with permissions to Kongregate or Facebook, they will be redirected to the login form.

    $platform->login();
    
Once the user is logged in, we can access data through the $platform

    $platform->getUserName();
    $platform->getFriends(); //returns empty array for Kongregate

## Microtransactions 

Kongregate has a [microtransactions api](http://www.kongregate.com/developer_center/docs/microtransaction-client-api "Transaction API Docs").
You can setup items for the api at /games/< username >/< game >/items
    
    $platform->getGameItems(); //gets an array returned by the api, as well as prices indexed by game_item name

    $data = $platform->getKredsInventory(); //api call to get the full inventory for a user
    $data['items']
    
    $platform->updateInventory(); //use all the items
    
    $platform->useKredItem($itemData); //api call to use a single item

The main example automatically requests the user item list from kongregate when the page loads with $platform->$getKredsInventory.  On the server side, we are automatically marking all items as used when the user loads the page, in order to cash them in.

## Examples

index.php is the basic example with login and microtransactions

## Testing

None so far, needs some phpunit.

## TODO

* support more of the api
* add tests

## Note on Patches/Pull Requests

* Fork the project.
* Make your feature addition or bug fix.
* Add tests for it. This is important so I don't break it in a
  future version unintentionally.
* Commit, do not mess with rakefile, version, or history.
  (if you want to have your own version, that is fine but bump version in a commit by itself I can ignore when I pull)
* Send me a pull request. Bonus points for topic branches.

## Copyright

Copyright (c) 2011 metal games, kongregate. See LICENSE for details.

