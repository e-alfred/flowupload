<template>

</template>

<script>
import NcContent from '@nextcloud/vue/dist/Components/NcContent'
import NcAppContent from '@nextcloud/vue/dist/Components/NcAppContent'
import NcAppNavigation from '@nextcloud/vue/dist/Components/NcAppNavigation'
import NcAppNavigationItem from "@nextcloud/vue/dist/Components/NcAppNavigationItem";
import NcAppNavigationNew from '@nextcloud/vue/dist/Components/NcAppNavigationNew'
import NcAppNavigationCounter from "@nextcloud/vue/dist/Components/NcAppNavigationCounter";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton";

import axios from "@nextcloud/axios";
//import { upload, Upload } from "@nextcloud/upload";
import { Uploader } from "./uploader/uploader.ts"
import { generateUrl } from "@nextcloud/router";

import UploadPicker from "./uploader/components/UploadPicker.vue";

export default {
	name: "App",
	components: {
		NcContent,
		NcAppContent,
		NcAppNavigation,
		NcAppNavigationItem,
		NcAppNavigationNew,
		NcAppNavigationCounter,
		NcActionButton,
        UploadPicker,
	},
}
