# Listable - Data Manipulation via Pipelines 
![](https://travis-ci.org/mrbobbybryant/listable.svg?branch=master)

Listables are a more functional approach to Arrays in PHP. Its also kinda like the [Lodash](https://lodash.com/) of PHP.

### WHY
Why would you build something like this? Doesn't PHP already have a lot of array methods built in? Yes it does. But this library does a few things differently.

- **Provides a consistent interface for all array methods.** PHP tends to flip the order of arguments. Sometimes the first arg is the haystack, sometimes it is the key. Since the array values are part of the Listable, you simple have to pass it the key, or callable function.
- **Provides a chainable interface.** Normalling if you want to conduct multiple array methods in a row, you have to nested the calls inside of each other, which can be hard to read. Listable allows you to chain method calls. Think of it like piping data through a list of transformations.
- **Provides additional array methods.** Out of the box PHP actually gives you a lot of methods for manipulating arrays. But lets be honest, there are still a ton of operations you do over and over again. Listable comes with a ton of additional array methods. Check out the API! 