PlinkerUI!
===================

A little PHP script which is a demonstration usage for Plinker - Remote coded tasks run as root! 

----------

Features:
---------

**What does it do?**

 - You can write bite-sized maintenance task directly on target nodes with just PHP or Bash scripting executed instantly or on per-second intervals as root user.
 - Create, delete and edit files inside `/var/www/html`.
 - It's very speedy! Instant and live updates through RPC, no need to poll the server when not in use.
 - Ive chosen to write it with a "no-framework" structure to allow people to understand it more.
 - Ive chosen to not apply a fancy theme, to keep assets and HTML less cluttered.

Nodes
-----

Nodes are any instances of the script, or at least the Plinker `plinker/core`, `plinker/tasks`, `plinker/system` components installed in your project code using composer.

**Screens**

![Section - Nodes](https://cherone.co.uk/files/screens/plinkerui/nodes.png)

![Section - Nodes Edit](https://cherone.co.uk/files/screens/plinkerui/node.edit.png)

----------

![Section - Nodes Files](https://cherone.co.uk/files/screens/plinkerui/node.tasks.png)
![Section - Nodes Files](https://cherone.co.uk/files/screens/plinkerui/node.files.png)
![Section - Nodes Files](https://cherone.co.uk/files/screens/plinkerui/node.information.png)


Tasks
-----

Configure bite-sized maintenance task directly on target nodes with simple PHP or Bash scripting. 

  -  Which are either executed instantly or on per-second intervals as **root** user.

**Screens**

![Section - Tasks](https://cherone.co.uk/files/screens/plinkerui/tasks.png)

![Section - Task](https://cherone.co.uk/files/screens/plinkerui/task.png)

![Section - Edit](https://cherone.co.uk/files/screens/plinkerui/task.edit.png)
