<?php
/*
 * FastSHL                              | Universal Syntax HighLighter |
 * ---------------------------------------------------------------------

   Copyright (C) 2002-2006  Juraj 'hvge' Durech

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

 * ---------------------------------------------------------------------
 * fshl_config.php
 *
 */

if(!defined('FSHL_WITH_TW_DEFINED'))
{
	define ('FSHL_WITH_TW_DEFINED', 1);

	define ('FSHL_CACHE',		FSHL_PATH.'fshl_cache/');
	define ('FSHL_LANG',		FSHL_PATH.'lang/');
	define ('FSHL_OUTMODULE',	FSHL_PATH.'out/');
	define ('FSHL_STYLE',		FSHL_PATH.'styles/');

	// fshlParser() 'option' flags (not used at this time)
	define ('P_TAB_INDENT',			0x0010);
	define ('P_LINE_COUNTER',		0x0020);
	define ('P_USE_NBSP',			0x0040);
	define ('P_STATISTIC',			0x1000);	// inject statistic for fshlGenerator class
	define ('P_DEFAULT',			0x0000);
	define ('P_DEFAULT_TAB_VALUE',	4);

	// state flags
	define ('PF_VOID',			0x0000);
	define ('PF_KEYWORD',		0x0001);
	define ('PF_RECURSION',		0x0004);
	define ('PF_NEWLANG',		0x0008);
	define ('PF_EXECUTE',		0x0010);

	// TW flags
	define ('PF_CLEAN',			0x0100);
	define ('PF_XIO',			0x0200);
	define ('PF_XDONE',			0x0400);
	define ('PF_XNEW',			0x0800);

	// state field indexes
	define ('XL_DIAGR',		0);
	define ('XL_FLAGS',		1);
	define ('XL_CLASS',		2);
	define ('XL_DATA',		3);

	define ('XL_DSTATE',	0);
	define ('XL_DTYPE',		1);

	// internal and special states
	define ('P_RET_STATE',	'_RET');
	define ('P_QUIT_STATE',	'_QUIT');

} //end if(!defined())
