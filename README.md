intracto-library
================

A simple symfony project to setup a reddit like way of sharing blogposts for internal use at www.intracto.com

![alt text](https://github.com/pix-art/ITRLibrary/blob/master/src/ITRLibraryBundle/Resources/public/img/example.png "Intracto Library")

Update 28-06-2016:
- Added slack integration
- Added search functionality
- Added weekly mail command

Update 14-07-2016:
- Added extra configuration for slack listener, you can now push certain tags to more channels

```
itr_library:
    slack:
        bot_name: "LibraryBot"
        default_channel: "library"
        channel_tags:
            - { channel: <channel>, tags: [<tag>, <tag>]}
```            