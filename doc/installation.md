# Installation on *nix

Downloading executable phar.

```sh
$ curl -sS http://apigen.org/installer | php
```

Move file to your `PATH`, so you can access it globally.

```sh
$ mv apigen.phar /usr/local/bin/apigen
```

Now you can simply run `apigen` instead of `php apigen.phar`.


# Installation on Windows

Change to your `PATH` directory  and run the install snippet to download apigen.phar:

```sh
C:\Users\username>cd C:\bin
C:\bin>php -r "readfile('http://apigen.org/installer');" | php
```

Create a new `apigen.bat` file alongside `apigen.phar`:

```sh
C:\bin>echo @php "%~dp0apigen.phar" %*>apigen.bat
```

Close your current terminal. Test usage with a new terminal:

```sh
C:\Users\username>apigen
ApiGen version v4.0.0
```
