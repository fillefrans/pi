Wire format

ØMQ messages are transmitted over TCP in frames consisting of an encoded payload length, followed by a flags field and the message body. The payload length is defined as the combined length in octets of the message body and the flags field.

For frames with a payload length not exceeding 254 octets, the payload length shall be encoded as a single octet. The minimum valid payload length of a frame is 1 octet, thus a payload length of 0 octets is invalid and such frames SHOULD be ignored.

For frames with a payload length exceeding 254 octets, the payload length shall be encoded as a single octet with the value 255 followed by the payload length represented as a 64-bit unsigned integer in network byte order.

The flags field consists of a single octet containing various control flags:

Bit 0 (MORE): More message parts to follow. A value of 0 indicates that there are no more message parts to follow; or that the message being sent is not a multi-part message. A value of 1 indicates that the message being sent is a multi-part message and more message parts are to follow.

Bits 1-7: Reserved. Bits 1-7 are reserved for future expansion and MUST be set to zero.

The following ABNF grammar represents a single frame:

    frame           = (length flags data)
    length          = OCTET / (escape 8OCTET)
    flags           = OCTET
    escape          = %xFF
    data            = *OCTET
The following diagram illustrates the layout of a frame with a payload length not exceeding 254 octets:

0                   1                   2                   3
0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
| Payload length|     Flags     |       Message body        ... |
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
| Message body ...
+-+-+-+-+-+-+- ...
The following diagram illustrates the layout of a frame with a payload length exceeding 254 octets:

0                   1                   2                   3
0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
|     0xff      |               Payload length              ... |
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
|                       Payload length                      ... |
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
| Payload length|     Flags     |        Message body       ... |
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
|  Message body ...
+-+-+-+-+-+-+-+ ...
