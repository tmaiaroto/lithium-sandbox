Lithium Sandbox
=======

A code sandbox for screen casts, tutorials, sharing, collaborating, etc.

### How it Works

Basically, you are getting your own copy of a tutorial web site. There's going to be helpful
information all over the application when you go to the home page. An internet connection is not
required to use and play around with this sandbox, but you'll likely want one.

The first rule of sandbox club is: You should never work off the master branch.
Each tutorial that you find out there will have its own branch! This means you can very easily load 
the required code for each tutorial without hurting anything. Be sure to keep your master branch 
up to date.

Then you can make your own local branches to play around with any side project. You could also then 
easily use a tutorial branch as a base for your experiments and so on. I think you get the idea.

Upon loading the application's home page, you will be instructed on what to do. Enjoy.

### Setup Instructions

This is going to be a little lengthy, but for those of you who are familiar with Lithium, you 
basically just want to get all the code here and setup your local dev environment as normal.
This sandbox primarily uses MongoDB so make sure you have that. You will see further information 
and instruction on the home page of this application.

So, the long version. You will need to clone this repo and then run a ```git submodule init``` and 
```git submodule update``` command. It's also important to note that some of these libraries have 
submodules themselves. You can try running something like ```git submodule update --init --recursive``` 
to get all submodules within the submodules. However, for your reference, currently the only 
libraries with submodules are li3_facebook and li3_qa. If there is any confusion or trouble, there 
is a text file within the libraries folder that lists the libraries you'll need and you could simply 
go track those down if need be.

It should be a snap to get all the code...And once you have all the code, you'll need to set this 
up on your web server. I personally prefer PHP-FPM and Nginx, however you can use Apache and normal 
PHP, but note you will need at least PHP version 5.3 or higher. If you're already familiar with 
Lithium, then the setup should be basically the same in terms of requirements.

If you compare this repo's file structure for the Lithium's "framework" repo you'll notice that 
there is no "app" directory. This is intentional and you don't actually need an "app" (or whatever 
name you like) directory. Namespace yes. Directory no. The ```config/bootstrap/libraries.php``` file 
has been updated to reflect this when it comes to the constants and include paths. This is a more 
compact setup for Lithium that puts essentially all libraries under the "libraries" directory.

So the server setup? You'll want to configure your server's conf, virtual host, etc. to use 
```/path/to/sandbox/webroot``` and you should be all set. The index.php file there is used.
If you need help setting up a local development environment or configuring any server for Lithium, 
I'll be sure to have a tutorial for that using Nginx and Apache in the future. In the meantime, you 
should be able to find some information via Google.

Last, you'll probably want MongoDB. Some tutorials will use MySQL, but MongoDB is our weapon of 
choice. It's extremely easy to download and run as a service for OS X, Linux, or Windows because 
you're basically using a package manager or downloading a binary. You have nothing to compile or 
even configure. You just run it. The ```config/bootstrap/connections.php``` file defines the default 
connection to MongoDB and uses the database called "sandbox" which you can change if you like. 
I would keep it the same if you can help it, because it could create confusion for you later on.

That's it! You're done. If you go to whatever your sub/domain is in your browser you should see the
sandbox's main screen. From there, you should have all the instruction you need to continue.