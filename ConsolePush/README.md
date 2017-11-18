ConsolePush is an ease-of-access tool that lets you use the console interface more conveniently. With ConsolePush, you can:

* [buffer the server output](#output-buffering)
* [prevent your trailing spaces from being trimmed](#anti-trimming)
* [cancel a command without clicking backsapce all the way](#cancelling)
* [wrap command lines](#line-pushing)

## Output buffering
When you type commands on console, have you ever found it annoying that server keeps printing lines like player joins and quits, interfering with your input? "Output buffering" (OB) will temporarily stop the server from printing anything on console, until you turn it off or type a command.

To enable OB, simply type `\` on console (and click &lt;Enter&gt;). The server will stop printing any lines.

When you send any commands (including a command that doesn't exist), OB is automatically disabled. You can also type `\` again (and &lt;Enter&gt;) to disable OB. Once OB is disabled, the lines previously hidden will be printed at once, so you won't miss any messages.

You can also type `\f` (and &lt;Enter&gt;) to "flush" the output, i.e. the lines previously hidden will be printed, but OB will not be disabled. This is similar to typing `\` twice.

> `\` and `\f` must be sent in individual lines, not at the beginning or end of other commanads.

## Anti trimming
PocketMine trims away the leading and trailing spaces in the command you sent, which is inconvenient if you want to send the spaces to the command. ConsolePush lets you do this by adding `\t` after your spaces.

For example, the command `"say hello world  "` normally only sends the message `"hello world"` without the trailing spaces, but `"say hello world  \t"` will include the two spaces before `\t` too.

## Cancelling
Sometimes you typed a long command, but then you decided not to type it and you don't want to keep backspace all the way until you cleared the line. ConsolePush lets you cancel the whole line of command by typing `\c` at the end of your command. For example, all the following lines do literally nothing (not even a "command not found" message):

```
help\c
say This is a very very very very very long message\c
ldkjsfdjkl \c
\c	
```

## Line pushing
Perhaps you forgot to enable OB before you started typing the command and your input is messed up, or something strange happened with your terminal and you don't know what you typed. _You don't have to type it again._ Just type `\p` (and &lt;Enter&gt;). Your input will be echoed on the next line, and you can continue typing it.

_Caution: `\p` does not allow editing lines that have been "pushed"._

`\p` can even be used in a chain, for example, if you type in this order:

```
say Hello \p
I continue typing \p
And I type many lines
```

Your terminal will end up looking like this:

```
say Hello \p
> say Hello I continue typing \p
> say Hello I continue typing And I type many lines
```

You will end up sending `"say Hello I continue typing And I type many lines"`.

If you want to cancel the whole line (including the pushed part), just type `\c` just like how you do with a single line.

If you typed `\p` but then realized you actually have nothing more to type, you can type `\n` &ndash; The command will be executed, without the `\n` at the end.

Note that PocketMine trims away the leading spaces in every line you type, including the line after pushed lines. If the line after `\p` shall start with a space, type a `\` before it, e.g.:

```
say Hello\p
\ Oops, there is no space behind "Hello", so I type the \ at the end.
```

The terminal will end up like this:

```
say Hello\p
> say Hello\ Oops, there is no space behind "Hello", so I typed the \ at the end.
```
This is equivalent to sending `say Hello Oops, there is no space behind "Hello", so I typed the \ at the end.`

## FAQ
- "What if I really want to send the `\p` at the end of my message?"
  - The answer is actually simple. Just type `\n` (which means "do things normally") after everything, Your command will be directly executed,
