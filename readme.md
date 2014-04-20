# TestDummy

Laughably simple database factories.

![image](https://dl.dropboxusercontent.com/u/774859/GitHub-Repos/testdummy/crashtestdummy.jpg)

```php
use Laracasts\TestDummy\Factory;

Factory::make('Post'); // make a Post with filled attributes

Factory::times(3)->create('Song'); // make and save three songs, along with all relationships
```

(More documentation coming soon.)