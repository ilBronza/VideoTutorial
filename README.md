# VideoTutorial

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

```bash
composer require ilbronza/videotutorial
```

## Usage

Run `php artisan migrate` to add route associations. In the video tutorial form, use **Associated routes** to select one or more named Laravel GET routes. For example, associating a guide with `orders.show` makes it available for every order detail page on that route.

In authenticated pages using the standard UIKit layout, a question-mark icon opens the guides for the current route. The panel includes active, visible tutorials with an uploaded video, an iframe embed, or a video link.

The catalog is available to signed-in users at `/video-tutorials/show`. A tutorial appears there when both **Active** and **Visible** are enabled. Published tutorials with a hidden parent appear at the top level. Administrators can open drafts and manage the hierarchy at `/manage-video-tutorials`.

The migration creates the web role `videoTutrialMaker`. Users assigned to this role can choose **Upload a video for this page** from the AccountManager profile menu. The upload creates a draft for the current named GET route and opens its edit form. The author can fill in the title and descriptions and enable **Active** and **Visible** to publish the guide.

If AccountManager route authorization is enabled, assign the role to the contextual upload and edit routes in its access catalog.

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

```bash
composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author@email.com instead of using the issue tracker.

## Credits

- [Author Name][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ilbronza/videotutorial.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ilbronza/videotutorial.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ilbronza/videotutorial/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/ilbronza/videotutorial
[link-downloads]: https://packagist.org/packages/ilbronza/videotutorial
[link-travis]: https://travis-ci.org/ilbronza/videotutorial
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/ilbronza
[link-contributors]: ../../contributors
