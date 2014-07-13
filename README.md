Pee on a Tree!
==============

*Be the top dog and mark your territory in this gamified call of nature*

<div style="text-align:center" markdown="1">

<img src="https://raw.githubusercontent.com/ballarat-hackerspace/PeeOnATree-Client/master/web/icons/poat_green.png" alt="Pee on a Tree!">

</div>


Pee on a Tree utilises local tree data in a foursquare/ingress style game encouraging families with kids, or kids at heart, to get out into the real world.
Visit some trees, learn a thing or two and crowdsource your local area database.

Players walk around their neighbourhood, looking for trees to mark.
When marked, you view information about the tree, such as the Genus and Species.

listing their team members, details about their project, what data sets they used and what competition categories (local and national) that they are going for


Values
------

* Education: promoting increased knowledge of the local area and trees.
* Caring: players can report any problems they notice with trees, leading to better care.
* Active lifestyles: players need to get around, go outside and view these trees.


Team: Ballarat Hackerspace
--------------------------
The team is from the [Ballarat Hackerspace](http://bhack.in/), and incorporated community group promoting the hacker lifestyle.
bHack is modeled on an international community of like minded spaces that started in Germany in 1995.
It supports and encourages science, technology, engineering, art and math culture in the Ballarat region by providing a physical space for residents to meet, interact and create. 


**Members**

* Brett James - Web Developer and system integration, live event (gaming) commentary/tech setups, photog, Pizza loving geek.
* Ian Firns - Electrical engineer and software developer.
* Josh Stewart - Programmer, commuter, funny red hoodie.
* Matt Barker - Support Engineer, dabbler in tech.
* Matt Weston - Software developer and technology enthusiast.
* Robert Layton - researcher investigating data analytics applied to cybercrime.
* Stuart Clark - Professional Drupal developer.


Datasets used
-------------
Our app uses tree data in GeoJSON format, with data used from Ballarat, Geelong and Melbourne regions.
We can extend to use this standardised format from other areas when that data becomes available.

This dataset provides information on trees, most importantly the location, species name and other data.


Technology
----------

On the frontend, we created a responsive app utilising jQuery, bootstrap, HTML5, Highchart, and Gravatar.
On the backend we used Fatfree PHP Framework, MySQL Database.
For analysis and data mangling, we utilised iPython notebooks.


Competition Categories
----------------------

Our app uses data in a fun and exciting format, providing information about the player's community and promotes a green and active lifestyle.

*National Prizes:*
The Best Open Government Data Hack

*Geelong/Ballarat Prizes:*

* Geelong - Open for Business, Open for Visits, Open For Living
* Best use of data to improve local government community services
* Best use of Vic Events data
* The Best City Accessibility Hack
* Best use of Victorian Government data
* Best video


Looking to the future....
-------------------------

We aim to include the following features:

* Accessibility! Showing users accessible trees and locations around them.
* Badges and rewards! Long term goals with achievements
* Double point events! Go to a tree near a local event for double points.
* Global events! Rain causes all active marks to go away, requiring people to remark their territory.


Code
----

Client (front-end): https://github.com/ballarat-hackerspace/PeeOnATree-Client

Server (back-end): https://github.com/ballarat-hackerspace/PeeOnATree-Server
