# wp-notice
The small OOP wrapper that allows working with WordPress notices nice and clean.

## Installation

```composer require rumur/wp-notice```

### Register into WordPress
It registers the renderer functionality, that will display all notifications added by the package. 
```php
<?php
// functions.php

\Rumur\WordPress\Notice\Notice::registerIntoWordPress();

// Alternative way of doing this.
add_action('admin_notices', [\Rumur\WordPress\Notice\Notice::class, 'render']); 

/**
 * The notice function that just need to return a string.
 *
 * @return string    
 */
function rmr_notice_as_function() {
    return '<p>Hello From Function</p>';
}

```

```php
<?php
// App/Notices/SimpleNotification.php

namespace App\Notices;

use Rumur\WordPress\Notice\Noticeable;

class SimpleNotification extends Noticeable
{
    public function message(): string
    {
        return '<p>I can change the info here</p>';
    }
}
```

### How to use?
There are several types that could be used.
`Notice::info(...)`, `Notice::error(...)`, `Notice::warning(...)`, `Notice::success(...)`.

Each of these methods could take as a param either a `string` or `Noticeable` or `\WP_Error` instances or regular callable function `rmr_notice_as_function` that returns a string.
```php
<?php

use App\Notices\SimpleNotification;
use App\Domain\Orders\OrderRepository;
use Rumur\WordPress\Notice\Notice;

$order = OrderRepository::find(2020); 

if ($order->isPayed()) {
    // Makes a success message from the Noticeable instance
    Notice::success(new SimpleNotification)
        // ⚠️ OPTIONAL ⚠️
        // Will be always displaying the notice, 
        // unless `DISABLE_NAG_NOTICES` defined and it's set as `true`
        // @link https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices#Disable_Nag_Notices
        ->nag()
        // ⚠️ OPTIONAL ⚠️
        // Makes a notice be dismissible. Adds a close button to the notice.
        ->dismissible()
        // ⚠️ OPTIONAL ⚠️
        // Makes a notice display it in an alternative way, 
        // by adding a background color that corresponds to its type.  
        ->asAlternative()
        // ⚠️ OPTIONAL ⚠️
        // This options tells to a notice to postpone itself and show only when time.
        ->showLater('tomorrow')
        // ⚠️ OPTIONAL ⚠️
        // This options tells to a notice to always show up until time.
        ->showUntil('next friday')
        // ⚠️ OPTIONAL ⚠️
        // Tells to a notice to show up only on this pages.
        ->showWhenPage('themes', 'tools' /*,...*/ )
        // ⚠️ OPTIONAL ⚠️
        // Tells to a notice to show up only if the logged in user has specific role.
        ->showWhenRole('subscriber', 'author' /*,...*/ )
        // ⚠️ OPTIONAL ⚠️
        // Tells to a notice to show up only if the logged in user has specific id.
        ->showWhenUser(1, get_user_by('id', 25) /*,...*/)
        // ⚠️ OPTIONAL ⚠️
        // Tells to a notice to show up only if the current screen is for specific taxonomies.
        ->showWhenTaxonomy('category', 'post_tag' /*,...*/ )
        // ⚠️ OPTIONAL ⚠️
        // Tells to a notice to show up only if the current screen is for specific post type.
        ->showWhenPostType('post', 'page' /*,...*/ );
}

```

### [Available methods](#available-methods)
| Recurrence                        |  Description                                                                 |
|-------------------------------    |------------------------------------------------------------------------      |
| `->nag();`                        | Makes nag notice                                                             |
| `->dismissible();`                | Makes be closable/dismissible                                                |
| `->showWhenRole(...string);`      | Tells to show when the current user role is mentioned                        |
| `->showWhenPage(...string);`      | Tells to show when the current screen page is mentioned                      |
| `->showWhenTaxonomy(...string);`  | Tells to show when the current screen is for specific taxonomies             |
| `->showWhenPostType(...string);`  | Tells to show when the current screen is for specific post types             |
| `->showWhenUser(...int/WP_User);` | Tells to show when the current user is mentioned                             | 
| `->showLater(...string/int(timestamp)/DateTimeInterface);`     | Tells to show the notice later when it's time   |
| `->showWhenUntil(...string/int(timestamp)/DateTimeInterface);` | Tells to show the notice until it's time        |

>Note, that all conditions are using `OR` operator to check, but if there is a time condition than it'll switch to `AND` instead.  

## To delete all traces when uninstalling plugin/theme which is used the package.
In order to delete all traces just call the `flush` method, like this `Notice::flush()`.   

## License
  This package is licensed under the MIT License - see the [LICENSE.md](https://github.com/rumur/wp-notice/blob/master/LICENSE) file for details.