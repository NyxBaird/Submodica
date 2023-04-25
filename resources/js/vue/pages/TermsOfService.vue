<style scoped lang="scss">
    @import 'resources/sass/variables';

    .termsRules {
        li {
            padding-bottom: 10px;
        }
    }
</style>

<template>
    <container id="Content">
        <h1>Website Terms and Conditions of Use</h1>

        <h2>1. Terms</h2>

        <p>By accessing this Website, accessible from https://submodica.xyz, https://submodica.net, and https://submodica.com, you are agreeing to be bound by these Website Terms and Conditions of Use and agree that you are responsible for adhering to any applicable local laws. If you disagree with any of these terms, you are prohibited from accessing this site.</p>

        <h2>2. Use License</h2>

        <p>Permission is granted to temporarily download one copy of the materials on Submodica's Website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>

        <ul>
            <li>modify or copy the materials;</li>
            <li>use the materials for any commercial purpose or for any public display;</li>
            <li>attempt to reverse engineer any software contained on Submodica's Website;</li>
            <li>remove any copyright or other proprietary notations from the materials; or</li>
            <li>transferring the materials to another person or "mirror" the materials on any other server.</li>
        </ul>

        <p>This will let Submodica terminate your account upon violations of any of these restrictions. Upon termination, your viewing right will also be terminated and you should destroy any downloaded materials in your possession whether they are printed or in electronic format.</p>

        <h2>3. Disclaimer</h2>

        <p>All the materials on Submodica’s Website are provided "as is", and we make no promises or guarantees about them. Submodica makes no warranties, may it be expressed or implied, and therefore negates all other warranties. Furthermore, Submodica does not make any representations concerning the accuracy or reliability of the use of the materials on its Website or otherwise relating to such materials or any sites linked to this Website.</p>

        <p>You understand and agree that we will not be liable to you or any third party for any loss of profits, use, goodwill, or data, or for any incidental, indirect, special, consequential or exemplary damages, however they arise.</p>

        <p>Submodica will do its best to moderate user content to ensure that it complies with both the terms of service and <a href="/rules">Submodica site rules</a>, but Submodica is in no way responsible for content posted by users.</p>

        <p>You are responsible for your use of the service. If you harm someone else or get into a dispute with someone else, we will not be involved.</p>

        <h2>4. User Code of Conduct</h2>

        <p>By using Submodica, you agree to follow <a href="/rules">the site rules</a> as well as the following:</p>
        <ul class="termsRules">
            <li>Do not post content you don't have the right to post. Submodica has zero tolerance for plagiarism. Users found posting content they don't have the rights to (this includes <i>explicit</i> permission to post on Submodica from authors and/or license holders of content you didn't create yourself) will face appropriate action including having all such content stripped from Submodica.</li>
            <li>Do not do anything that may expose Submodica or its users to any type of harm, including anything that may disrupt, disable, tamper with, overburden or limit the functionality of Submodica.</li>
            <li>Do not post any form of content or link to any website that contains anything that is illegal, misleading, malicious, harassing, inaccurate, discriminatory or otherwise objectionable or inappropriate or which violates any applicable laws. Submodica has the right to prescreen, monitor, or remove user content for any reason or no reason- but has no obligation to do so.</li>
        </ul>

        <h2>5. Limitations</h2>

        <p>Submodica or its suppliers will not be held accountable for any damages that will arise with the use or inability to use the materials on Submodica’s Website, even if Submodica or an authorize representative of this Website has been notified, orally or written, of the possibility of such damage. Some jurisdiction does not allow limitations on implied warranties or limitations of liability for incidental damages, these limitations may not apply to you.</p>

        <h2>6. Revisions and Errata</h2>

        <p>The materials appearing on Submodica’s Website may include technical, typographical, or photographic errors. Submodica will not promise that any of the materials in this Website are accurate, complete, or current. Submodica may change the materials contained on its Website at any time without notice. Submodica does not make any commitment to update the materials.</p>

        <h2>7. Links</h2>

        <p>Submodica has not reviewed all of the sites linked to its Website and is not responsible for the contents of any such linked site. The presence of any link does not imply endorsement by Submodica of the site. The use of any linked website is at the user’s own risk.</p>

        <h2>8. Site Terms of Use Modifications</h2>

        <p>Submodica may revise these Terms of Use for its Website at any time without prior notice. By using this Website, you are agreeing to be bound by the current version of these Terms and Conditions of Use.</p>

        <h2>9. Your Privacy</h2>

        <p>Please read our <a href="/privacy-policy">Privacy Policy</a>.</p>

        <h2>10. Governing Law</h2>

        <p>Any claim related to Submodica's Website shall be governed by the laws of the state of Arizona, USA, without regards to its conflict of law provisions.</p>

        <h2>11. Miscellaneous</h2>

        <p> - Force Majeure. Neither party will be liable for failure or delay in performance to the extent caused by circumstances beyond its reasonable control, including acts of God, natural disasters, terrorism, riots, or war.</p>
        <p> - No Agency. This Agreement does not create any agency, partnership, or joint venture between the parties.</p>
        <p> - Severability. If any part of this Agreement is invalid, illegal, or unenforceable, the rest of the Agreement will remain in effect.</p>
        <p> - No Third-Party Beneficiaries. This Agreement does not confer any benefits on any third party unless it expressly states that it does.</p>
    </container>
</template>

<script>
import Container from "../components/Container.vue";

export default {
    name: "TermsOfService",
    components: {
        "container": Container
    }
}
</script>
