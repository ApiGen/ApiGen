# ApiGen - TokenReflection version

This is an unstable development branch. Use it for testing only. Anything can change anytime :)

However this is the future of our ApiGen fork - not relying on the internal reflection (and having to include and parse the processed files) but using the TokenReflection library (which will be released soon) and emulating the reflection interface using only the tokenized PHP source. This approach has a couple of disadvantages but a huge number of advantages. And some of them are pretty damn cool :)

When this branch achieves certain level of stability (we have set ourselves some goals internally), we plan to ask several friends of ours to test it in real environment. And if those tests turn out OK, we will celebrate for a week or two, merge this branch to the master and continue development there.

Unfortunately, when this happens, it will mean that there will not be many pull requests to the original library any longer. The reason is simple - our fork and the original library do not have many common parts now (the parsing Model is completely different, there are different template helpers, there are many incompatible changes in templates, …) and there will be even more differences with future development.


A&K