import { getGettextBuilder } from '@nextcloud/l10n/dist/gettext'

type Translation = {
	locale: string;
	json: object;
}

const gtBuilder = getGettextBuilder()
	.detectLocale()

const translations = (process.env.TRANSLATIONS as unknown as Array<Translation>)
translations.forEach(data => gtBuilder.addTranslation(data.locale, data.json))

const gt = gtBuilder.build()

export const n = gt.ngettext.bind(gt)
export const t = gt.gettext.bind(gt)
