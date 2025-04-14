<script setup>
import {ref} from "vue";

const props = defineProps({
    conversation: {
        type: Array,
        default: () => [],
    },
});

const activePanels = ref(props.conversation.map((_, index) => index));
</script>

<template>
    <Accordion v-model="activePanels" multiple>
        <AccordionPanel
            v-for="(email, index) in conversation"
            :key="index"
            :value="index"
        >
            <AccordionHeader>
                <div>
                    <div>{{`${email.subject} - ${email.from}`}}</div>
                    <div class="text-xs">{{ email.date }}</div>
                </div>
            </AccordionHeader>
            <AccordionContent>
                <div>
                    <div><strong>From:</strong> {{ email.from }}</div>
                    <div><strong>To:</strong> {{ email.to }}</div>
                    <div><strong>Date:</strong> {{ email.date }}</div>

                    <div><strong>Body:</strong></div>
                    <div v-html="email.body_html"></div>
                </div>
            </AccordionContent>
        </AccordionPanel>
    </Accordion>
</template>

<style scoped>

</style>
