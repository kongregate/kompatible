# synapse

An api for hosting games on both Kongregate and Facebook

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
    
An examples of this is in index.php.

## User login

The call to login a user is easy. If they aren't currently logged in with permissions to Kongregate or Facebook, they will be redirected to the login form.

    $platform->login();
    
Once the user is logged in, we can access data through the $platform

    $platform->getUserName();
    $platform->getFriends(); //returns empty array for Kongregate

## Load the Site Specific API

If your game needs to use the site specific javacript api (to submit stats, make purchase requests, etc) then we'll need to load those libraries:

    $platform->loadLibraries();

## Serving your game file

For flash games, you should customize the $platform->getFlashParams() function. These are the parameters to pass along to the flash game itself.

    public function getFlashParams() {
    	$params = "&user_id={$this->user}";
    	return $params;
    }

Then you can display the flash file in the page with:

    $platform->displayFlashFile();

## Kongregate Microtransactions 

KongregatePlatform.php is setup to check the api on first request to see if the user has purchased any items. 

If they do, then we automatically marking all the items as used.

First, we get a list of all our available items from the server. (/games/< username >/< game >/items)

    $platform->getGameItems();

We can get a users specific inventory:

    $data = $platform->getKredsInventory();
    $data['items']
    
If we want to mark all the items as used:

    $platform->updateInventory();

Or if we want to use a single item on page load:
    
    $platform->useKredItem($itemData);

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
* Add tests for it. This is important so I don't break it in a
  future version unintentionally.
* Commit, do not mess with rakefile, version, or history.
  (if you want to have your own version, that is fine but bump version in a commit by itself I can ignore when I pull)
* Send me a pull request. Bonus points for topic branches.

## Copyright

Copyright (c) 2011 metal games, kongregate. See LICENSE for details.

