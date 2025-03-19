# Extension shims

## Extension:Echo

With BlueSpice 4.5, we do not bundle Extension:Echo anymore. But the Extension:LoginNotify has a hard dependeny on Extension:Echo. To make it possible to use Extension:LoginNotify, we provide a shim for Extension:Echo.

To enable the shim, use the following configuration in `LocalSettings.php`:

```php
wfLoadExtension( 'Echo', 'extensions/BlueSpiceProDistributionConnector/extension-shims/Echo/extension.json' );
```