Commando.io - Dev-Ops Evolved.
------------------------------

![main-header-image](http://netdna.commando.io/images/main-header.png)

Commando.io is a web-based interface for streamlining the use of SSH for deployments and system administration tasks across groups of remote servers.

GitHub fundamentally changed the way developers use revision control by creating a beautiful user interface and social platform. Commando.io does the same for managing servers & dev-op tasks.

The goal of Commando.io is to make it super simple to execute commands on groups of servers and visualize the results. Additionally Commando.io provides IT compliance and accountability, as every command executed is logged with who executed what, when, and why. Finally all commands are versioned and centrally stored.

Screenshots & Additional Details
--------------------------------

[ ![Web servers tagged and grouped.](http://netdna.commando.io/images/screenshots/small/servers.png) ](http://netdna.commando.io/images/screenshots/xlarge/servers.png)
[ ![Adding a recipe.](http://netdna.commando.io/images/screenshots/small/add-recipe.png) ](http://netdna.commando.io/images/screenshots/xlarge/add-recipe.png)
[ ![Executing a recipe on a group of servers.](http://netdna.commando.io/images/screenshots/small/execute.png) ](http://netdna.commando.io/images/screenshots/xlarge/execute.png)
[ ![Upload and transfer text or binary files.](http://netdna.commando.io/images/screenshots/small/files.png) ](http://netdna.commando.io/images/screenshots/xlarge/files.png)

#### See http://commando.io for additional details. ####

Important Notes
---------------

#### This is a very early alpha build of Commando.io. For security, do not expose Commando.io publicly! The following important features of the software have not been implemented: ####

* Users and log-in. **Again, please do not expose Commando.io publicly**. Run it locally, and use web-server authentication for now. A fully featured users and log-in system is coming.
* SSH connections and executions still happen via `PHP` using the `ssh2` extension. This is going to be replaced with a separate dedicated `node.js` SSH worker using websockets. PHP will not make SSH connections and executions in the future.

Requirements
------------

#### Web-Server ####
**Nginx**, **Lighttpd**, or **Apache**

#### PHP ####
Version **5.3.0** or greater.  
*(Version 5.4.0 or greater for json pretty print support when viewing execution history).*

#### PHP Extensions ####
+ **mysqli**
+ **json**
+ **curl**
+ **mongo** (https://github.com/mongodb/mongo-php-driver)
+ **ssh2** (http://pecl.php.net/package/ssh2)

#### MySQL####
Version **5.0** or greater running the **InnoDB** storage engine. *MyISAM is NOT supported.*

#### MongoDB + GridFS ####
Version **2.0** or greater is highly recommended. Older versions of MongoDB may work.

Installation
------------

Right now installation is a bit involved and brutal, but once we iron out Commando.io a bit further, we will re-work `/install.php` and make installation much simpler.

**1.)** Clone the repo, `git clone git://github.com/nodesocket/commando.git`, or [download the latest release](https://github.com/nodesocket/commando/tarball/master).

**2.)** Execute `$ php -f install.php`, or view `/install.php` via a browser. *The installer script requires write access to the filesystem to copy and update configuration files.*

**3.)** Delete the installer script `/install.php`. *It should not present a security risk, but it is still recommended to delete it.*

**4.)** Add the public and private SSH keys you wish to connect with into the `/keys` directory. It is **highly recommended** to set the permission on both keys to `0400`; read only. Also, make sure the keys are user-owned by the user that executes PHP.

**5.)** Edit `/app.config.php` and provide the correct paths for:

SSH_PUBLIC_KEY_PATH
SSH_PRIVATE_KEY_PATH

**6.)** Create a user in MySQL to connect with.

**7.)** Edit `/classes/MySQLConfiguration.php` and provide the connection details to MySQL.

**8.)** Import the MySQL schema located in `/schema/latest.sql` into MySQL.

```` bash
$ mysql --user=USERNAME --pass=PASSWORD --host=SERVERHOST < /schema/latest.sql
````

**9.)**	Assign the MySQL user created above to the newly imported database `commando`.    

**10.)** Create a database `commando` and a collection `executions` in MongoDB. *If you need MongoDB hosting check out https://mongohq.com or https://mongolab.com.*


**11.)** Create the following standard indexes on the `executions` collection in MongoDB:   

```` bash
db.executions.ensureIndex({ "executed" : 1 },{ "groups" : 1 },{ "recipes.id" : 1 },{ "servers.id" : 1 },{ "recipes.interpreter" : 1 });
````

**12.)** Create a user in MongoDB to connect with.

**13.)** Edit `/classes/MongoConfiguration.php` and provide the connection details to MongoDB.

**14.)** *(OPTIONAL)* This step is not required, but if you want to enable *pretty links* you must setup some rules on your web-server:

````
Pretty links enabled: /view-recipe/rec_c4Bb4E01Q0d8a37N4bU37
````

````
Pretty links disabled: /view-recipe.php?param1=rec_c4Bb4E01Q0d8a37N4bU37
````

##### Nginx #####
```` nginx
location ~ ^[^\.]+$ {
	fastcgi_index index.php;
	fastcgi_intercept_errors on;
	fastcgi_pass unix:/var/run/php-fpm/php.sock;
	include /etc/nginx/fastcgi_params;
	fastcgi_param SCRIPT_FILENAME $document_root/controller.php;
	fastcgi_param SCRIPT_NAME /controller.php;
	fastcgi_param PATH_INFO $uri;
}
````

##### Lighttpd #####
```` lighttpd
$HTTP["host"] =~ "^(your-domain-here\.com)$" {
	url.rewrite-once = (
		"^[^\.]+$" => "controller.php/$1"
	)
}
````

Recipe Markup (rMarkup)
-----------------------

rMarkup is a form of procedural markup written inside recipes. The syntax is:

````
{{method:value}}
````

### Specification ###

  <table>
   <thead>
      <tr>
        <th>Method <i>(case insensitive)</i></th>
        <th>Value</th>
        <th>Example</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>include</td>
        <td>A 25 character recipe ID.</td>
        <td>{{include:rec_Xb4LI504839d1a6078eF6}}</td>
      </tr>
    </tbody>
  </table>
  
### Include Method ###

> Include a recipe by ID.
>  
> The contents of the included recipes are injected at execution. You may include multiple recipes, or even include the same recipe multiple times. Only include recipes that use the same interpreter, this restraint is checked and enforced at execution. Multi-level includes are not currently supported, i.e. an included recipe may not include other recipes itself.
>
> ````
> # bash - include recipe example
> echo "hello"
> {{include:rec_Xb4LI504839d1a6078eF6}}
> echo "world"
````
>
>````
># bash - rec_Xb4LI504839d1a6078eF6
>free -m
>````

Current Version
---------------

https://github.com/nodesocket/commando/blob/master/VERSION

Changelog
---------

https://github.com/nodesocket/commando/blob/master/CHANGELOG.md

Support, Bugs, And Feature Requests
-----------------------------------

Create issues on GitHub (https://github.com/nodesocket/commando/issues).

Versioning
----------

For transparency and insight into our release cycle, and for striving to maintain backward compatibility, Commando.io will be maintained under the semantic versioning guidelines.

Releases will be numbered with the follow format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

+ Breaking backward compatibility bumps the major (and resets the minor and patch)
+ New additions without breaking backward compatibility bumps the minor (and resets the patch)
+ Bug fixes and misc changes bumps the patch

For more information on semantic versioning, visit http://semver.org/.

Contact
-------

+ http://commando.io
+ commando@nodesocket.com
+ http://twitter.com/commando_io

Sponsored By
------------

[ ![NetDNA](http://netdna.commando.io/images/netdna.png) ] (http://netdna.com)

Follow [@NetDNADeveloper](http://www.twitter.com/netdnadeveloper) on Twitter.

License & Legal
---------------

Copyright 2012 NodeSocket, LLC

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this work except in compliance with the License. You may obtain a copy of the License in the LICENSE file, or at:

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
