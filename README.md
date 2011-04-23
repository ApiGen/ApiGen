# APIGen - TokenReflection version

This is an unstable development branch. Use it for testing only. Anything can change anytime :)

However this is the future of our Apigen fork - not relying on the internal reflection (and having to include and parse the processed files) but using the TokenReflection library (which will be released soon) and emulating the reflection interface using only the tokenized PHP source. This approach has a couple of disadvantages but a huge number of advantages. And some of them are pretty damn cool :)

Stay tuned, play with it and let us know what you think. Any feedback will be appreciated.


A&K


Bottom line: When this branch gets merged into master, it will mean the end of our pull requests to the original library (btw, none of them was accepted yet, which is not very motivating anyway). After this step, there won't be much common in our fork and the original library (the parsing Model is completely different, there are different template helpers, there are many incompatible changes in templates, …).