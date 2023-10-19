# Retrofit for Drupal

The Retrofit provides compatibility layers for legacy Drupal code to allow run on any version of Drupal.

## Installation

Retrofit is _not_ a module. All you need to do is install the package using Composer and Retrofit is available and working!

```shell
composer require retrofit-drupal/retrofit
```

And that's it! 🎉

## How it works

This library registers a service provider to integrate with Drupal automatically. No extra configuration is needed. Once Retrofit has been added to your Drupal code base it will automatically provide backward compatibility layers for you.

## Support

If you would like free and public support, open a [Discussion](https://github.com/retrofit-drupal/retrofit/discussions/new?category=q-a). You can also join the [#retrofit](https://drupal.slack.com/archives/C05BT6LALUR) channel on [Drupal Slack](https://www.drupal.org/community/contributor-guide/reference-information/talk/tools/slack) as well.

If you would like paid and private support, [contact Matt Glaman](https://mglaman.dev/contact-matt) directly. Opportunities for paid private support are coming.

## Usage

Currently, the compatibility layers are drop-in replacements. This means you can use them in your code without any
changes. Some functions are namespaced for compatibility.

[Get started](docs/getting-started.md) with Retrofit for Drupal
