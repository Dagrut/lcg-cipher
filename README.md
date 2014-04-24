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

Usages
------

	./lcgc cipher|decipher <password> [input file [output file]]

Cipher or decipher the given `input file` to the `output file` (standard 
input/output if none, or if equal to '-')

	./lcgc prng <password> <count>

Generate `count` pseudo-random numbers for the given password.

	./lcgc debug <password>

Print the values used for each LCG.

	php src/lcgc.php cycles <password>

Computes the cycle length of the given password.

The same arguments can be used with the C++ and php version, except for 
"cycles", which is only in the php version.

Idea
----

The idea of this algorithm is to have a ciper key as long as the input data. 
To achieve this, I thought that using pseudo-random number generators would 
be a nice idea. However, on 32 bit computers, you may not be able to have a 
cycle long enough to cipher some documents. To compensate this, I'm adding 
the output of several pseudo-random number generators. I could have used any 
algorithm for this, but the linear congruential generators seemed more 
reliable to me for this algorithm.

You may also be interested in [this 
document](http://research.ijcaonline.org/volume50/number19/pxc3880973.pdf), 
which seems to be based on a similar idea, but with a block cipher.

Principle
---------

The idea is quite simple : We take a password, hash it with a sha512 (the 
one provided by php, and the version I took 
[here](https://github.com/routar/C-SHA2) for the C++ version), then we split 
the hash into several parts, each one containing 4 sub-parts. These 4 
sub-parts are 4 numbers used to initiate the LCG. The LCG are stored in an 
array, and when we need a random number, we just have to make the sum of all 
LCG values.

When we have to cipher a message, at the very beginning of the message we 
will prepend a really random sequence of 16 bytes, taken from /dev/urandom 
(and not /dev/random, mainly for speed improvement). It should help making 
the deciphering harder for an attacker. To see the complete process, look at 
the `cipher()` function.

The deciphering is almost the same thing. the variables p1 and p2 are just 
switched since they are generated in a specific order (see the constructors).

The hashing with sha512 is just a shortcut that I use to produce enough data 
for the LCGs. Any other hashing algorithm could have performed well, but I 
don't know other algorithms with such a big output. Concerning the division 
of the hash to produce the LCG seeds, I use 5 blocks of 4 times 3 bytes 
each, and then one block of 4 times 1 byte. Other patterns should work well 
too, if the picked values are big enough.

**Important :** Note that when you cipher something as one block of data, 
you can only decipher it in one step too. Each time the program is started, 
the input is handled as a flow, which must not be breaked. The state could 
be saved and restored later, but that was not my main goal, and storing it 
somewhere may compromise the algorithm security.

Drawbacks
---------

This algorithm may have several drawbacks :
* If the LCG input values are not good, the pseudo-random generator cycle may 
  be too short for some files. Using prime numbers as modulos reduce this 
  probability, but may not completely avoid it.
* The beginning of the ciphered data is the weakest point of the algorithm,
  that's why a random sequence is added at the beginning of the input. 
  However, if the random number generator does not produce real random 
  numbers, the output (or a part of it) may be guessed more easily.
* Because mathematics are not my field of expertise, I could miss some flaws 
  in this algorithm, so use it at your own risk!

Performances
------------

This script does not run really fast, for several reasons :
* The input is processed bytes per bytes.
* It is a mono-process script.
* It does not have/use the optimisations found in other algorithms like x86 AES
  instructions.

Remarks & comments
------------------

For any remark or comment, you would be glad to use github :
[https://github.com/Dagrut/lcg-cipher](https://github.com/Dagrut/lcg-cipher).
