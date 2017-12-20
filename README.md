### About 

FileCommitAnimator creates a GIF out of a file's lifetime in a Github repository.

A response to this [SomebodyMakeThis prompt](https://www.reddit.com/r/SomebodyMakeThis/comments/4t4ia4/smt_a_tool_that_would_make_an_animation_of_a/).

Version 1.0 made on the weekend of 2016-07-18.

### Install

```shell
composer install
```

### Run

Windows:
```shell
./run.bat
```

UNIX:
```shell
./run.sh
```

### Application

```
Welcome to the FileCommitAnimator! You'll need a Github account to continue.

Enter Github Email: your@email.com
Enter Password: *********************

--File Details--
Repository Owner Username: jtoy
Repository Name: awesome-tensorflow
File Path: README.md

--Gif Configuration--
Width (px): 1000
Height (px): 1200
Frame rate (per second): 60

Retrieving commits... done.
Progress: 98/98 frames completed.
Creating gif... gifs/2016-07-18 12-34-22pm.gif created.
```
[Sample Gif](sample/2016-07-18%2012-34-22pm.gif?raw=true)

### Dependencies

* PHP >=5.3
* GD
* [PHP-PhantomJS](https://github.com/Dachande663/PHP-PhantomJS)
* [AnimGif](https://github.com/lunakid/AnimGif)
