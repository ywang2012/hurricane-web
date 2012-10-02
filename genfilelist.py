#!/usr/bin/env python
import sys
import link_data 

link_data._debug = 1
link_data.genfilelist(sys.argv[1])
