<script setup>
import {usePage} from "@inertiajs/vue3";
import {useTranslation} from "../composables/useTranslation.js";

const page = usePage();
// const { translate } = useTranslation();
// const props = defineProps({
//     title: {
//         type: String,
//         default: null,
//     }
// });
</script>

<template>
    <div class="container mx-auto p-4">
        <p v-if="$page.props.auth.user">Welcome, {{ page.props.auth.user.name }}</p>
<!--       <h1 class="text-2xl">-->
<!--           {{ translate('modules.home.h1', {title: props.title}) }}-->
<!--       </h1>-->

<!--        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-5">-->
<!--&lt;!&ndash;            <Card>&ndash;&gt;-->
<!--&lt;!&ndash;                <template #title>&ndash;&gt;-->
<!--&lt;!&ndash;                    {{ translate('modules.home.downloaded_files_count', { count: page.props.downloaded_files_count }) }}&ndash;&gt;-->
<!--&lt;!&ndash;                </template>&ndash;&gt;-->
<!--&lt;!&ndash;            </Card>&ndash;&gt;-->
<!--&lt;!&ndash;            <Card>&ndash;&gt;-->
<!--&lt;!&ndash;                <template #title>&ndash;&gt;-->
<!--&lt;!&ndash;                    {{ translate('modules.home.favorited_files_count', { count: page.props.favorited_files_count }) }}&ndash;&gt;-->
<!--&lt;!&ndash;                </template>&ndash;&gt;-->
<!--&lt;!&ndash;            </Card>&ndash;&gt;-->
<!--        </div>-->
    </div>
</template>
