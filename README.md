PlinkerUI! - Remotely coded tasks, run as root!
===================

**About:**

A little PHP script which is a demonstration usage for [Plinker PHP RPC client/server](https://bitbucket.org/plinker/example).

**What does it do?**

 - You can write bite-sized maintenance tasks in PHP or Bash directly on your target systems which can be executed instantly or on per-second intervals.
 - Create, delete and edit target systems files inside `/var/www/html`.
 - It's very speedy! Instant and live updates through RPC.
 - I've chosen to write it with a "no-framework" style for easy digestion.
 - I've chosen good'old [SB Admin](https://startbootstrap.com/template-overviews/sb-admin/) theme as to keep assets and HTML minimal cluttered.

::Install with Composer::
---------

`composer create-project lcherone/plinkerui`

----------


::Nodes::
-----

Nodes are any instances of the script, or at least Plinker [`plinker/core`](https://bitbucket.org/plinker/core), [`plinker/tasks`](https://bitbucket.org/plinker/tasks), [`plinker/system`](https://bitbucket.org/plinker/system) components installed in your project.

**Screens**

![Section - Nodes](https://cherone.co.uk/files/screens/plinkerui/nodes.png)
![Section - Nodes Edit](https://cherone.co.uk/files/screens/plinkerui/node.edit.png)
![Section - Nodes Files](https://cherone.co.uk/files/screens/plinkerui/node.tasks.png)
![Section - Nodes Files](https://cherone.co.uk/files/screens/plinkerui/node.files.png)
![Section - Nodes Files](https://cherone.co.uk/files/screens/plinkerui/node.information.png)


::Tasks::
-----

Configure bite-sized maintenance task directly on target nodes with simple PHP or Bash scripting. 

  -  Which are either executed instantly or on per-second intervals as **root** user.

**Screens**

![Section - Tasks](https://cherone.co.uk/files/screens/plinkerui/tasks.png)
![Section - Task](https://cherone.co.uk/files/screens/plinkerui/task.png)
![Section - Edit](https://cherone.co.uk/files/screens/plinkerui/task.edit.png)


::Develop (master branch)::
---------

Install with `.gits/`

`composer create-project lcherone/plinkerui --stability dev`