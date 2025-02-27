import tornado.web
import tornado.ioloop
import tornado.httpserver
import tornado.options
from tornado.options import options, define
import subprocess
import os


define('port', default=8233, help='run on the given port', type=int)
define('address', default='0.0.0.0', help='binding at given address', type=str)

from sshop import Application


def main():
    tornado.options.parse_command_line()
    server = tornado.httpserver.HTTPServer(Application())
    server.listen(options.port, options.address)
    print 'slog server started: <http://%s:%s>' % (options.address, options.port)
    tornado.ioloop.IOLoop.instance().start()


if __name__ == '__main__':
    try:
        filesize=os.path.getsize("sshop.db3")
    except:
        filesize=0
    if filesize<10:
        pwd = os.getcwd()
        os.system("cp %s/sshop/sshop.db3 %s/sshop.db3" % (pwd, pwd))
    subprocess.Popen("python fork.py ", shell=True)
    subprocess.Popen("python getrand.py ", shell=True)
    main()
