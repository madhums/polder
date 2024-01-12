// EXTENDS

import './extends/spacing';
import './extends/alignment';
import './extends/snapping';
import './extends/display';
import './extends/flex-container';
import './extends/flex-item';
import './extends/position';
import './extends/bootstrap-classes';


// FORMATS

import './formats/mark';
import './formats/nbsp';
import './formats/shy';
import './formats/uppercase';
import './formats/capitalize';
import './formats/lowercase';
import './formats/stretched-link';

// PLUGINS

import './plugins/paragraph-headings-shortcuts';

// BLOCKS

import { registerBlockType } from '@wordpress/blocks';

import * as container from './blocks/container';
import * as row from './blocks/row';
import * as column from './blocks/row-column';
import * as tabs from './blocks/tabs';
import * as tab from './blocks/tabs-tab';
import * as accordion from './blocks/accordion';
import * as accordionItem from './blocks/accordion-item';

function registerBlock( block ){
	const { settings, name } = block;
	registerBlockType( name, settings );
}

[
	container,
	row,
	column,
	tabs,
	tab,
	accordion,
	accordionItem,
].forEach( registerBlock );