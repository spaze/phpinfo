# phpinfo
Extract `phpinfo()` into a variable and move CSS to external file.

This might be handy when you want to show the output from `phpinfo()` to authenticated users only in your site's layout for example.

```php
$phpInfo = new \Spaze\PhpInfo\PhpInfo();
$html = $phpInfo->getHtml();
```

## `getHtml()`
The `getHtml()` method returns the `phpinfo()` output, without the HTML `head` and `body` elements, wrapped in `<div id="phpinfo">` & `</div>`.

All inline CSS will be "externalized" to CSS classes, you can load `assets/info.css` to get the colors back.

An example usage with Nette Framework (can be used with other frameworks or standalone, too):
```php
$this->template->phpinfo = Html::el()->setHtml($this->phpInfo->getHtml());
```

Please note that this will also remove the HTML `head` element which contains `meta name="ROBOTS"` tag preventing search engines and other bots indexing the `phpinfo()` output.
You have to add it back somehow, for example by rendering the `getHtml()` output in your own layout which includes the `head` element with the `meta name="ROBOTS"` tag.
In general, `phpinfo()` output should be accessible only for authenticated users.

## `getFullPageHtml()`
Sometimes, you may want to display the classic `phpinfo()` output, with the original HTML `head` and `body` elements, `meta name="ROBOTS"` tag, inline styles etc.,
but still with the sensitive info sanitized (see below). In that case, you may use `getFullPageHtml()`:
```php
$phpInfo = new \Spaze\PhpInfo\PhpInfo();
echo $phpInfo->getFullPageHtml();
```

## Sanitization
By default, session id will be automatically determined and replaced by `[***]` in the output.
This is to prevent some session hijacking attacks that would read the session id from the cookie value reflected in the `phpinfo()` output
(see my [blog post](https://www.michalspacek.com/stealing-session-ids-with-phpinfo-and-how-to-stop-it) describing the attack, `HttpOnly` bypasses, and the solution).
You can disable the sanitization by calling `doNotSanitizeSessionId()` but it's totally not recommended. Do not disable that. Please.

You can add own strings to be sanitized in the output with
```php
addSanitization(string $sanitize, ?string $with = null): self
```
If found, the string in `$sanitize` will be replaced with the string `$with`, if `$with` is null then the default `[***]` will be used instead.

Some of the values in `phpinfo()` output are printed URL-encoded, so the `$sanitize` value will also be searched URL-encoded automatically.
This means that both `foo,bar` and `foo%2Cbar` would be replaced.

The sanitizer will try to determine the session id and sanitize it automatically, you can (but shouldn't) disable it with `doNotSanitizeSessionId()`.

The following values will be used when determining the session id:
1. `session_id()` output if not `false`
2. `$_COOKIE[session_name()]` if it's a string

However, it is not recommended to rely solely on the automated way, because for example you may set the session name somewhere in a custom service,
and it may not be available for the sanitizer to use. I'd rather suggest you configure the sanitization manually:
```php
$phpInfo->addSanitization($this->sessionHandler->getId(), '[***]'); // where $this->sessionHandler is your custom service for example
```
or
```php
$phpInfo->addSanitization($_COOKIE['MYSESSID'], '[***]'); // where MYSESSID is your session name
```
or something like that.

## Sanitizing arbitrary strings
If you have your `phpinfo()` output (or anything really) in a string, you can use the sanitizer standalone, for example:
```php
$sanitizer = new \Spaze\PhpInfo\SensitiveValueSanitizer();
$string = $sanitizer->addSanitization('🍍', '🍌')->sanitize('🍍🍕');
```

You can then pass the configured sanitizer to `PhpInfo` class which will then use your configuration for sanitizing the `phpinfo()` output too:
```php
$phpInfo = new \Spaze\PhpInfo\PhpInfo($sanitizer);
$html = $phpInfo->getHtml();
```
