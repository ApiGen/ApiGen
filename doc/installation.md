# Installation on *nix

After downloading phar file, move it to your `PATH`, so you can access it globally.

```sh
$ mv apigen.phar /usr/local/bin/apigen
```

Now you can simply run `apigen` instead of `php apigen.phar`.


# Installation on Windows

Move phar file to your `PATH` directory.

```sh
C:\Users\username>cd C:\bin
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
