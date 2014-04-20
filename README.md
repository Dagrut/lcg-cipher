LCG Cipher
==========

This program is a proof of concept. It is meant to cipher/decipher any data. 
It can read and write from stdin/stdout or from files.

Structure
---------

This script is divided into two classes : LCG, which is a simple [Linear 
Congruential number 
Generator](http://en.wikipedia.org/wiki/Linear_congruential_generator), and 
LCGCipher, which currently allows you to cipher and decipher data. The other 
parts of the scripts are trivial (parameters reading, processing loops, ...).

Usage
-----

	php lcg_cipher.php <cipher|decipher> <password> [input file [output file]]

The input and output files can be left empty or can be equal to '-' for 
standard input and output.

Idea
----

The idea of this algorithm is to have a ciper key as long as the input data. 
To achieve this, I thought that using pseudo-random number generators would 
be a nice idea. However, on a 32 bits computer, you may not be able to have 
a cycle long enough to cipher some documents. To compensate this, I'm adding 
the output of several pseudo-random number generators. I could have used any 
algorithm for this, but the linear congruential generators seems to be more 
reliable for this algorithm.

You may also be interested in [this 
document](http://research.ijcaonline.org/volume50/number19/pxc3880973.pdf), 
which seems to be based on a similar idea, but with a block cipher.

Principle
---------

The idea is quite simple : We take a password, hash it with a sha512 (the 
one provided by php, probably sha-2), then we split the hash into several 
parts, each one containing 4 sub-parts. These 4 sub-parts are the 4 numbers 
used to initiate the LCG. The LCG are stored in an array, and when we need a 
random number, we just have to make the sum of all LCG values.

When we have to cipher a message, at the very beginning of the message we 
will add a really random sequence (its size depends of the value given by the 
internal RNG), taken from /dev/urandom (and not /dev/random, mainly for 
speed improvement). It should help making the deciphering harder for an 
attacker. To see the complete process, look at the `cipher()` function.

The deciphering is almost the same thing. the variables p1 and p2 are just 
switched since they are generated in a specific order (see the `__construct`).

The hashing with sha512 is just a shortcut that I use to produce enough data 
for the LCGs. Any other hashing algorithm could have performed well, but I 
don't know other algorithms with such a big output. Concerning the division 
of the hash to produce the LCG seeds, I use 5 blocks of 4 times 3 bytes 
each, and then one block of 4 times 1 byte. Other patterns should work well 
too, if the picked values are big enough.

**Important :** Note that when you cipher something as one block of data, 
you can only decipher it one step too. Each time the program is started, the 
input is handled as a flow, which must not be breaked. The state could be 
saved and restored later, but that was not my main goal, and storing it 
somewhere may compromise the algorithm security.

Drawbacks
---------

This algorithm may have several drawbacks :
* If the LCG input values are not good, the pseudo-random generator cycle may 
  be too short for some files. However, it seems that with sha512, the chances 
  that something like this happens are very small.
* The beginning of the ciphered data is the weakest point of the algorithm,
  that's why a random sequence is added at the beginning of the input. 
  However, if the random number generator does not produce real random 
  numbers, the output (or a part of it) may be guessed more easily.
* Because mathematics are not my field of expertise, I could miss some flaws 
  in this algorithm, so use it at your own risk!

Performances
------------

This script does not run really fast, for several reasons :
* Too small legs... Huh, no, forget it!
* The input is processed bytes per bytes.
* It is a mono-process script.
* It is a script and not a compiled program.
* It does not have/use the optimisations found in other algorithms like x86 AES
  instructions.

If I write a C or C++ version, I'll try to benchmark it and compare it with 
other programs and algorithms.

Remarks & comments
------------------

For any remark or comment, you would be glad to use github :
[https://github.com/Dagrut/lcg-cipher](https://github.com/Dagrut/lcg-cipher).
