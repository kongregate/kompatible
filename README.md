# synapse

Putting a Facebook game on Kongregate

Kongregate can serve games through your servers just like Facebook. Metal games, the producers of Tyrant, have given us some tips and code for handling both. With some small tweaks to your server, and some tweaks to your game, you can serve your game in both places.

The code examples in this demo are in php from https://github.com/jimgreer/synapse

Handling kongregate and facebook credentials:

First, create a Kongregate game in iframe mode, using the url of index.php from synapse. Then you can get your game credentials from kongregate at /games/<username>/<game>/api. For the demo, you can put them in the top of index.php.

## Usage

  if (isset($_REQUEST['platform']) && $_REQUEST['platform'] == "fb") {
    …
  } else {
    require_once("KongregatePlatform.php");
    $config['app_id'] = "<id>";
    $config['app_secret'] = "<api_key>";
    $config['app_root'] = "";
    $config['server_root'] = "<wherever your server is>";
    $platform = new KongregatePlatform($config);
  }

## Handling user login

The php api automatically shows a link to the kongregate login form if they aren’t logged in. You can change the content of this in kongregate_login.php. When they are successful, the example app just sends the user back to the main index.php.

From there, we can access $platform for kongregate and facebook, for getting the user name, or user items.



## Handling microtransactions 

You can make items on kongregate with [/games/<username>/<game>/items]

    //display a purchase button in the page
    $platform->purchaseButton();

    $data = $platform->getKredsInventory(); //api call to get the full inventory for a user
    $data['items'] //an array of the items and ids
    
    $platform->updateInventory(); //use all the items
    
    $platform->useKredItem($itemData); //api call to use a single item
    


The main example automatically requests the user item list from kongregate when the page loads with $platform->$getKredsInventory.  On the server side, we are automatically marking all items as used when the user loads the page, in order to cash them in.

kreds_game.php example.

link to transaction api docs.

## Testing

    None so far

## TODO

* support more of the api

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

