# TestDummy

![image](https://dl.dropboxusercontent.com/u/774859/GitHub-Repos/testdummy/crashtestdummy.jpg)

TestDummy makes the process of preparing your database for integration tests as easy as possible. ...As easy as:

### Make a post model with dummy attributes.

```php
use Laracasts\TestDummy\Factory;

$post = Factory::build('Post');
```

### Build a song entity, along with any set relationships, and save it to the database.

```php
use Laracasts\TestDummy\Factory;

$song = Factory::create('Song');
```

### Create and persist three comment entities

```php
use Laracasts\TestDummy\Factory;

Factory::times(3)->create('Song');
```

### Create three posts, but override the default title

```php
use Laracasts\TestDummy\Factory;

Factory::times(3)->create('Post', ['title' => 'My Title']);
```

## Usage

### Step 1: Install

Pull this package in through Composer, per usual:

```js
"require-dev": {
    "laracasts/testdummy": "1.*"
}
```

### Step 2: Create a fixtures.yml file

TestDummy will fetch the default values for each of your entities from a `fixtures.yml` file that you place in your tests directory. If using Laravel, this file may be added anywhere under the `app/tests`
directory. Here's an example of a `Post` and an `Author`.

```ruby
Post:
  title: Hello World $string
  body: $text
  published_at: $date
  author_id:
    type: Author

Author:
    name: John Doe $integer
```

Notice that we can use `$string`, `$text`, `$date`, and `$integer` placeholders. Behind the scenes, TestDummy will leverage the Faker library to insert dynamic data.

Pay attention to how we reference relationships, such as `author_id` above. You need to let TestDummy know the type of its associated model. TestDummy will then automatically create and save that relationship as well.

```ruby
  author_id:
    type: Author
```

### Step 3: Setup

When testing against a database, it's recommended that you recreate your environment for each test. That way, you can protect yourself against false positives. A SQLite database (maybe even one in memory) is a good choice in these cases.

If using Laravel with a DB in memory, you might do:

```php
public function setUp()
{
    parent::setUp();

    Artisan::call('migrate');
}
```

Better yet, place this code in a parent class, which you can then extend.

### Step 4: Write Your Tests

You're all set to go now. Start testing! Here's some code to get you started. Assuming that you have a `Post` and `Comment` model created...

```php

use Laracasts\TestDummy\Factory;

$comment = Factory::create('Comment');
```

This will create and save both a `Comment`, as well as a `Post` record to the database.

Or, maybe you need to write a test to ensure that, if you have three songs with their respective lengths, when you call a `getTotalLength` method on the owning `Album` model, it will return the correct value. That's easy!

```php
// create three songs, and explicitly set the length
Factory::times(3)->create('Song', ['length' => 200]);

$album = Album::first(); // this will be created once automatically.

$this->assertEquals(600, $album->getTotalLength());
```


